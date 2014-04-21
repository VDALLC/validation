define(['jquery', './validators'], function($, validators) {
    function Messages()
    {

    }

    Messages.prototype.first = function(field)
    {
        if (this.field) {
            // get first element
            for (var i in this.field) {
                return this.field[i];
            }
        }
        return null;
    };

    function ArrayValidator()
    {
        // 2 dimension [field name][rule name] = validator specification (not object)
        // assumed there is no duplicated rules for each field
        this.validationRules = {};
        // numeric indexes
        this.activeFields = [];
        // numeric indexes
        this.activeRules = [];
        // named indexes
        this.errorMessages = {};
    }

    ArrayValidator.customValidators = {}; // named indexes

    ArrayValidator.registerRule = function(rule, callback)
    {
        ArrayValidator.customValidators[rule] = new validators.callback(callback);
    };

    ArrayValidator.prototype._setRule = function(field, spec)
    {
        var name = this._checkValidationSpec(spec);
        this.validationRules[field] = this.validationRules[field] || {};
        this.validationRules[field][name] = spec;
    };

    ArrayValidator.prototype.configure = function(rules, messages, labels)
    {
        var field;
        for (field in rules) {
            for (var i in rules[field]) {
                this._setRule(field, rules[field][i]);
            }
        }

        for (var rule in messages) {
            this.errorMessages[rule] = messages[rule];
        }

        // TODO labels
    };

    ArrayValidator.prototype.field = function(fields, rules)
    {
        this.activeFields = Array.isArray(fields) ? fields : [fields];
        this.activeRules = rules ? (Array.isArray(rules) ? rules : [rules]) : [];
        for (var i in this.activeFields) {
            for (var j in this.activeRules) {
                var field = this.activeFields[i];
                var rule = this.activeRules[j];
                this._setRule(field, rule);
            }
        }

        return this;
    };

    ArrayValidator.prototype.required = function()
    {
        this.activeRules = ['required'];
        for (var i in this.activeFields) {
            var field = this.activeFields[i];
            this._setRule(field, 'required');
        }

        return this;
    };

    ArrayValidator.prototype.range = function(from, to)
    {
        this.activeRules = ['range'];
        for (var i in this.activeFields) {
            var field = this.activeFields[i];
            this._setRule(field, ['range', from, to]);
        }

        return this;
    };

    ArrayValidator.prototype.message = function(template)
    {
        for (var i in this.activeRules) {
            var rule = this.activeRules[i];
            this.errorMessages[rule] = template;
        }

        return this;
    };

    /**
     *
     * @param data
     * @param ok function(validData) {...}
     * @param fail function(messages) {...}
     */
    ArrayValidator.prototype.validate = function(data, ok, fail)
    {
        var results = [], self = this;

        for (var field in this.validationRules) {
            var rules = this.validationRules[field];
            for (var rule in rules) {
                var spec = rules[rule];
                var validator = this._createValidator(spec);
                var value = this._getValue(data, field);
                // validator.validate() cat return either {isValid: *, value: *} or promise.
                // see http://api.jquery.com/category/deferred-object/
                var promise = validator.validate({
                    value: value,
                    field: field,
                    data: data,
                    rule: rule
                });
                results.push(promise);
            }
        }

        $.when.apply($, results).done(
            function(
                /* arguments will be a list of 'results' items
                (validation results as resolved promises) */
            ) {
                var isValid = true, validData = {}, messages = new Messages();
                for (var i in arguments) if (arguments.hasOwnProperty(i)) {
                    var res = arguments[i];
                    if (!res.field) throw new Error('Invalid validator response');
                    if (res.isValid) {
                        validData[res.field] = res.value;
                    } else {
                        isValid = false;
                        validData[res.field] = null;
                        messages[res.field] = messages[res.field] || {};
                        messages[res.field][res.rule] = self._makeMessage(res.rule, res.field, {/* TODO */});
                    }
                }

                isValid ? ok(validData) : fail(messages);
            }
        );
    };

    ArrayValidator.prototype._getValue = function(data, key)
    {
        key = key.split('.');
        var res = data;
        for (var i in key) {
            res = res && res[key[i]];
        }
        return res;
    };

    ArrayValidator.prototype._makeMessage = function(rule, field, params)
    {
        if (this.errorMessages[rule]) {
            params.field = field;
            return this._fillTemplate(this.errorMessages[rule], params);
        } else {
            return field + " invalid";
        }
    };

    ArrayValidator.prototype._fillTemplate = function(template, params)
    {
        var res = template;
        for (var i in params) {
            res = res.replace('#' + i + '#', params[i]);
        }
        return res;
    };

    ArrayValidator.prototype._checkValidationSpec = function(spec)
    {
        if (typeof(spec) == 'string') {
            return this._fetchValidatorByName(spec) ? spec : false;
        } else if (Array.isArray(spec)) {
            return this._fetchValidatorByName(spec[0]) ? spec[0] : false;
        } else {
            throw new Error('invalid validator specified #' + spec);
        }
    };

    ArrayValidator.prototype._createValidator = function(spec)
    {
        var constructor, args = [];
        if (typeof(spec) == 'string') {
            constructor = this._fetchValidatorByName(spec);
        } else if (Array.isArray(spec)) {
            var name = spec[0];
            args = spec.slice(1);
            constructor = this._fetchValidatorByName(name);
        } else {
            throw new Error('invalid validator specified #' + spec);
        }

        if (typeof(constructor) == 'object') {
            return constructor;
        } else {
            return this._createObject(constructor, args);
        }
    };

    ArrayValidator.prototype._createObject = function(constructor, args)
    {
        if (Array.isArray(args)) {
            switch (args.length) {
                case 0:
                    return new constructor();
                case 1:
                    return new constructor(args[0]);
                case 2:
                    return new constructor(args[0], args[1]);
                case 3:
                    return new constructor(args[0], args[1], args[2]);
                case 4:
                    return new constructor(args[0], args[1], args[2], args[3]);
                default:
                    throw new Error('Unsupported argument length #' + args);
            }
        } else {
            return new constructor();
        }
    };

    ArrayValidator.prototype._fetchValidatorByName = function(name)
    {
        if (ArrayValidator.customValidators[name]) {
            return ArrayValidator.customValidators[name];
        } else {
            return validators[name];
        }
    };

    return ArrayValidator;
});
