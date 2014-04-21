define(['jquery'], function($) {
    function ErrorRenderer(jForm, nextCallback)
    {
        this.jForm = jForm;
        this.nextCallback = nextCallback;
    }

    ErrorRenderer.prototype.getCallback = function()
    {
        var self = this;

        return function(messages)
        {
            $('.error', self.jForm).remove();

            for (var name in messages) {
                for (var rule in messages[name]) {
                    var message = messages[name][rule];
                    $('[name=' + name + ']', self.jForm).after(
                        '<span class="error ' + rule +'">' + message + '</span>'
                    );
                }
            }

            if (self.nextCallback) {
                return self.nextCallback(messages);
            } else {
                return false;
            }
        };
    };

    return ErrorRenderer;
});