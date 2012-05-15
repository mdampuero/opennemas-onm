if(!OpenNeMas) {
    var OpenNeMas = {};
}

OpenNeMas.Maxlength = Class.create({

    initialize: function(elm, options) {
        this.element = $(elm);
        this.ranges = options.ranges || [{range: $R(0, 34), color: '#EEFFEE' },
                                         {range: $R(35, 79), color: '#FFEE99' },
                                         {range: $R(80, 149), color: '#FFCCCC'},
                                         {range: $R(150, 250), color: '#AA1111'}];
        this.maxlength = options.maxlength || 250;

        this.element.observe('change', this.onKeyPress.bindAsEventListener(this));
        this.element.observe('keypress', this.onKeyPress.bindAsEventListener(this));

        this.element.setAttribute('maxlength', this.maxlength);

        this.setBgColor();
    },

    onKeyPress: function() {
        this.setBgColor();
    },

    setBgColor: function() {
        var lng = this.element.value.length;
        this.ranges.each(function(item) {
            if(item.range.include(lng)) {
                this.element.setStyle({'backgroundColor': item.color});
                throw $break;
            }
        }, this);
    }
});