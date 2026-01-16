/**
 * Smart Search for Anchor Documentation
 * Features: Fuzzy matching, keyword synonyms, relevance scoring
 */

class SmartSearch {
    constructor(docs, contentIndex = [], keywordMap = {}) {
        this.docs = docs;
        this.contentIndex = contentIndex;
        this.keywords = keywordMap;
        console.group('SmartSearch Initialized');
        console.log('Docs:', this.docs.length);
        console.log('Content Index:', this.contentIndex.length);
        console.log('Keyword Map:', Object.keys(this.keywords).length);
        console.groupEnd();
    }

    /**
     * Calculate Levenshtein distance for fuzzy matching
     */
    levenshteinDistance(str1, str2) {
        const len1 = str1.length;
        const len2 = str2.length;
        const matrix = Array(len1 + 1).fill(null).map(() => Array(len2 + 1).fill(0));

        for (let i = 0; i <= len1; i++) matrix[i][0] = i;
        for (let j = 0; j <= len2; j++) matrix[0][j] = j;

        for (let i = 1; i <= len1; i++) {
            for (let j = 1; j <= len2; j++) {
                const cost = str1[i - 1] === str2[j - 1] ? 0 : 1;
                matrix[i][j] = Math.min(
                    matrix[i - 1][j] + 1,      // deletion
                    matrix[i][j - 1] + 1,      // insertion
                    matrix[i - 1][j - 1] + cost // substitution
                );
            }
        }

        return matrix[len1][len2];
    }

    /**
     * Calculate similarity score (0-1)
     */
    similarity(str1, str2) {
        const maxLen = Math.max(str1.length, str2.length);
        if (maxLen === 0) return 1.0;
        const distance = this.levenshteinDistance(str1, str2);
        return 1.0 - (distance / maxLen);
    }

    /**
     * Get related keywords for a search term
     */
    getRelatedKeywords(term) {
        const related = new Set([term]);
        const lowerTerm = term.toLowerCase();

        // Add exact matches from keyword map
        if (this.keywords[lowerTerm]) {
            this.keywords[lowerTerm].forEach(keyword => related.add(keyword));
        }

        // Add fuzzy matches
        Object.keys(this.keywords).forEach(key => {
            // Relaxed threshold to 0.65 to catch more variations (e.g. collect vs collection)
            if (this.similarity(lowerTerm, key) > 0.65) {
                console.debug(`Fuzzy match found: ${lowerTerm} ~ ${key}`);
                this.keywords[key].forEach(keyword => related.add(keyword));
            }
        });

        return Array.from(related);
    }

    /**
     * Calculate relevance score for a document
     */
    calculateScore(doc, query) {
        const queryLower = query.toLowerCase();
        const titleLower = doc.title.toLowerCase();
        const nameLower = doc.name.toLowerCase();
        
        let score = 0;

        // Exact match in title (highest priority)
        if (titleLower === queryLower) {
            score += 100;
        }

        // Starts with query
        if (titleLower.startsWith(queryLower)) {
            score += 45;
        }

        // Contains query
        if (titleLower.includes(queryLower)) {
            score += 25;
        }

        // Exact match in name
        if (nameLower === queryLower) {
            score += 90;
        }

        // Contains in name
        if (nameLower.includes(queryLower)) {
            score += 20;
        }

        // Fuzzy match on title
        const titleSimilarity = this.similarity(queryLower, titleLower);
        if (titleSimilarity > 0.6) {
            score += titleSimilarity * 35;
        }

        // Fuzzy match on name
        const nameSimilarity = this.similarity(queryLower, nameLower);
        if (nameSimilarity > 0.6) {
            score += nameSimilarity * 25;
        }

        // Content-based matching (NEW)
        const contentDoc = this.contentIndex?.find(d => d.name === doc.name);
        if (contentDoc) {
            const queryWords = queryLower.split(/\s+/);
            
            // Check if query appears in content keywords
            queryWords.forEach(qWord => {
                if (contentDoc.keywords.includes(qWord)) {
                    score += 20; // High score for content keyword match
                }
                
                // Fuzzy match against content keywords
                contentDoc.keywords.forEach(keyword => {
                    const sim = this.similarity(qWord, keyword);
                    if (sim > 0.75) {
                        score += sim * 15;
                    }
                });
            });
            
            // Check description
            if (contentDoc.description.toLowerCase().includes(queryLower)) {
                score += 15;
            }
        }

        // Word-by-word matching
        const queryWords = queryLower.split(/\s+/);
        const titleWords = titleLower.split(/\s+/);
        
        queryWords.forEach(qWord => {
            titleWords.forEach(tWord => {
                if (tWord.includes(qWord) || qWord.includes(tWord)) {
                    score += 5;
                }
                const wordSim = this.similarity(qWord, tWord);
                if (wordSim > 0.7) {
                    score += wordSim * 10;
                }
            });
        });

        // Keyword synonym matching
        const relatedKeywords = this.getRelatedKeywords(queryLower);
        relatedKeywords.forEach(keyword => {
            if (titleLower.includes(keyword) || nameLower.includes(keyword)) {
                score += 20;
            }
            
            // Check against content keywords
            if (contentDoc?.keywords.includes(keyword)) {
                score += 25;
            }
        });

        // Cap score at 100
        return Math.min(Math.round(score), 100);
    }

    /**
     * Search documents with smart matching
     */
    search(query) {
        if (!query || query.trim().length === 0) {
            return [];
        }

        const results = this.docs
            .map(doc => {
                const score = this.calculateScore(doc, query);
                const contentDoc = this.contentIndex?.find(d => d.name === doc.name);
                
                return {
                    ...doc,
                    score,
                    description: contentDoc?.description || ''
                };
            })
            .filter(doc => doc.score > 0)
            .sort((a, b) => b.score - a.score)
            .slice(0, 10); // Top 10 results

        return results;
    }

    /**
     * Highlight matching text in results
     */
    highlightMatch(text, query) {
        if (!query) return text;

        const queryLower = query.toLowerCase();
        const textLower = text.toLowerCase();
        
        // Find all matching positions
        const matches = [];
        let pos = textLower.indexOf(queryLower);
        
        while (pos !== -1) {
            matches.push({ start: pos, end: pos + query.length });
            pos = textLower.indexOf(queryLower, pos + 1);
        }

        // Also match individual words
        const queryWords = queryLower.split(/\s+/);
        queryWords.forEach(word => {
            if (word.length < 2) return;
            let pos = textLower.indexOf(word);
            while (pos !== -1) {
                matches.push({ start: pos, end: pos + word.length });
                pos = textLower.indexOf(word, pos + 1);
            }
        });

        // Sort and merge overlapping matches
        matches.sort((a, b) => a.start - b.start);
        const merged = [];
        matches.forEach(match => {
            if (merged.length === 0 || merged[merged.length - 1].end < match.start) {
                merged.push(match);
            } else {
                merged[merged.length - 1].end = Math.max(merged[merged.length - 1].end, match.end);
            }
        });

        // Build highlighted string
        if (merged.length === 0) return text;

        let result = '';
        let lastEnd = 0;
        
        merged.forEach(match => {
            result += text.substring(lastEnd, match.start);
            result += '<strong>' + text.substring(match.start, match.end) + '</strong>';
            lastEnd = match.end;
        });
        
        result += text.substring(lastEnd);
        return result;
    }
}

// Export for use in templates
if (typeof window !== 'undefined') {
    window.SmartSearch = SmartSearch;
}
