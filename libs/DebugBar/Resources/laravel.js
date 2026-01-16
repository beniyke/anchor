if (typeof(PhpDebugBar) !== 'undefined') {
    // Custom Widget for displaying lists of JSON objects with pretty-print and toggle
    var JsonListWidget = PhpDebugBar.Widget.extend({
        className: 'phpdebugbar-widgets-list',
        render: function() {
            this.bindAttr('data', function(data) {
                this.$el.empty();
                if (!data) return;

                var self = this;
                $.each(data, function(key, value) {
                    var li = $('<li />').addClass('phpdebugbar-widgets-list-item').appendTo(self.$el);
                    
                    // Title/Key
                    var $title = $('<div />').css({
                        cursor: 'pointer',
                        fontWeight: 'bold'
                    }).text(key).appendTo(li);

                    // Content (JSON)
                    if (value) {
                        var content = value;
                        try {
                            // If it's a string, try to parse it to ensure it's valid JSON for pretty printing
                            if (typeof content === 'string') {
                                var parsed = JSON.parse(content);
                                content = JSON.stringify(parsed, null, 4);
                            } else {
                                content = JSON.stringify(content, null, 4);
                            }
                        } catch (e) {
                            // If parsing fails, just show as is
                        }

                        var $attr = $('<pre />').addClass('phpdebugbar-widgets-list-item-value').css({
                            marginTop: '5px',
                            display: 'none',
                            backgroundColor: '#f5f5f5',
                            padding: '5px',
                            borderRadius: '3px',
                            border: '1px solid #ddd'
                        }).text(content).appendTo(li);

                        $title.click(function() {
                            $attr.toggle();
                        });
                    }
                });
            });
        }
    });

    // Register the widget
    PhpDebugBar.Widgets.JsonListWidget = JsonListWidget;
}
