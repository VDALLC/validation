define([], function() {
    function Required() {}

    Required.prototype.validate = function(params)
    {
        params.isValid = !!params.value;
        return params;
    };

    function Number() {}

    Number.prototype.validate = function(params)
    {
        // http://stackoverflow.com/questions/18082/validate-numbers-in-javascript-isnumeric
        params.isValid = !isNaN(parseFloat(params.value)) && isFinite(params.value);
        return params;
    };

    function Range(from, to)
    {
        this.from = from;
        this.to = to;
    }

    Range.prototype.validate = function(params)
    {
        params.value = +params.value;
        params.isValid = params.value >= this.from && params.value <= this.to;
        return params;
    };

    function Callback(handler)
    {
        this.handler = handler;
    }

    Callback.prototype.validate = function(params)
    {
        return this.handler(params);
    };

    return {
        required: Required,
        number: Number,
        range: Range,
        callback: Callback
    };
});
