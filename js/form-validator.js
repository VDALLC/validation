define(['jquery', './array-validator', './error-renderer'], function($, ArrayValidator, ErrorRenderer) {
    function FormValidator(formSelector, ok, fail)
    {
        var self = this;

        var form = this.form = $(formSelector);

        ok = ok || function() { return true; };

        fail = fail || (new ErrorRenderer(form)).getCallback();

        if (form.size() != 1) {
            throw new Error('No form or several forms selected');
        }

        var av = this.arrayValidator = new ArrayValidator();
        $('[data-validation]', this.form).each(function() {
            var el = $(this);
            var name = el.attr('name');
            var spec = el.attr('data-validation');
            av.field(name, self._parseDataValidation(el, spec));
        });

        form.submit(function() {
            av.validate(self._getFormData(this), ok, fail);
            return false;
        });
    }

    FormValidator.prototype._getFormData = function(form)
    {
        var res = {};
        var data = $(form).serializeArray();
        for (var i in data) {
            res[ data[i].name ] = data[i].value;
        }
        return res;
    };

    FormValidator.prototype._parseDataValidation = function(element, spec)
    {
        switch (spec) {
            case 'required':
                return 'required';
            default:
                throw new Error('Unknown validation spec: ' + spec);
        }
    };

    FormValidator.initAllForms = function()
    {
        $('form').each(function() {
            new FormValidator(this);
        });
    };

    return FormValidator;
});