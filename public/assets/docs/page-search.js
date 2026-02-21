/**
 * Anchor Framework Documentation - In-Page Search
 * Provides a native find-in-page experience with themed highlighting.
 */
class PageSearch {
    constructor(contentSelector = '.content') {
        this.contentArea = document.querySelector(contentSelector);
        this.matches = [];
        this.currentIndex = -1;
        this.query = '';

        // UI Elements
        this.bar = document.getElementById('pageSearchBar');
        this.input = document.getElementById('pageSearchInput');
        this.count = document.getElementById('pageSearchCount');
        this.prevBtn = document.getElementById('pageSearchPrev');
        this.nextBtn = document.getElementById('pageSearchNext');
        this.closeBtn = document.getElementById('pageSearchClose');

        this.init();
    }

    init() {
        if (!this.bar || !this.input) return;

        // Input listener with debounce
        let timeout;
        this.input.addEventListener('input', (e) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => this.performSearch(e.target.value), 200);
        });

        // Navigation listeners
        this.prevBtn.addEventListener('click', () => this.navigate(-1));
        this.nextBtn.addEventListener('click', () => this.navigate(1));
        this.closeBtn.addEventListener('click', () => this.close());

        // Keyboard navigation within bar
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.navigate(e.shiftKey ? -1 : 1);
            } else if (e.key === 'Escape') {
                this.close();
            }
        });

        // Global hotkey Ctrl+F
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'f') {
                e.preventDefault();
                this.open();
            }
        });
    }

    open() {
        this.bar.classList.add('active');
        setTimeout(() => {
            this.input.focus();
            this.input.select();
        }, 50);
    }

    close() {
        this.bar.classList.remove('active');
        this.clear();
        this.input.value = '';
    }

    clear() {
        // Remove all marks
        const marks = this.contentArea.querySelectorAll('.search-match');
        marks.forEach(mark => {
            const parent = mark.parentNode;
            parent.replaceChild(document.createTextNode(mark.textContent), mark);
            parent.normalize(); // Merge adjacent text nodes
        });

        this.matches = [];
        this.currentIndex = -1;
        this.updateUI();
    }

    performSearch(query) {
        this.query = query.trim();
        this.clear();

        if (this.query.length < 2) return;

        this.highlight(this.contentArea, this.query);
        this.matches = Array.from(this.contentArea.querySelectorAll('.search-match'));

        if (this.matches.length > 0) {
            this.currentIndex = 0;
            this.goToMatch(0);
        }

        this.updateUI();
    }

    highlight(root, query) {
        const regex = new RegExp(`(${this.escapeRegExp(query)})`, 'gi');
        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
        const nodes = [];

        while (walker.nextNode()) {
            const node = walker.currentNode;
            // Skip script/style/pre tags if you want, but highlighting code is often good
            if (node.parentNode.tagName === 'SCRIPT' || node.parentNode.tagName === 'STYLE') continue;
            if (node.textContent.search(regex) >= 0) {
                nodes.push(node);
            }
        }

        const checkRegex = new RegExp(`^${this.escapeRegExp(query)}$`, 'i');
        nodes.forEach(node => {
            const fragment = document.createDocumentFragment();
            const parts = node.textContent.split(regex);

            parts.forEach(part => {
                if (checkRegex.test(part)) {
                    const mark = document.createElement('mark');
                    mark.className = 'search-match';
                    mark.textContent = part;
                    fragment.appendChild(mark);
                } else if (part.length > 0) {
                    fragment.appendChild(document.createTextNode(part));
                }
            });

            node.parentNode.replaceChild(fragment, node);
        });
    }

    navigate(direction) {
        if (this.matches.length === 0) return;

        this.currentIndex += direction;
        if (this.currentIndex >= this.matches.length) this.currentIndex = 0;
        if (this.currentIndex < 0) this.currentIndex = this.matches.length - 1;

        this.goToMatch(this.currentIndex);
        this.updateUI();
    }

    goToMatch(index) {
        // Clear active state
        this.matches.forEach(m => m.classList.remove('search-match-active'));

        const match = this.matches[index];
        if (!match) return;

        match.classList.add('search-match-active');

        // Scroll into view with buffer
        const headerOffet = 110;
        const elementPosition = match.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffet;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }

    updateUI() {
        if (this.matches.length === 0) {
            this.count.textContent = '0/0';
            this.count.style.color = this.query.length >= 2 ? '#ef4444' : 'var(--text-tertiary)';
        } else {
            this.count.textContent = `${this.currentIndex + 1}/${this.matches.length}`;
            this.count.style.color = 'var(--text-tertiary)';
        }
    }

    escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
}
