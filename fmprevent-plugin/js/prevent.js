(function(){

    FMPrevent={};
    render={};
    FMPrevent.Models={};
    FMPrevent.Collections={};
    FMPrevent.Views={};
    FMPrevent.Templates={};
    FMPrevent.Models.Connector = Backbone.Model.extend();
    FMPrevent.Collections.Connectors = Backbone.Collection.extend({model:FMPrevent.Models.Connector});

    Handlebars.registerHelper('if_eq', function(a, b, opts) {
    if(a == b) // Or === depending on your needs
        return opts.fn(this);
    else
        return opts.inverse(this);
});

Backbone.Model.prototype.toJSON = function() {
 
        var clone = _.clone(this.attributes);
        _.each(clone, function (attr, idx) {
          if(attr.toJSON){
            clone[idx] = attr.toJSON();
          }
          
        });
        return clone;
      
};

   FMPrevent.Templates.Cable=Handlebars.compile('<div id="cable-body" style="position:absolute;top:214px;left:390px"><input type="text" id="cable-length" placeholder="lunghezza">'+
      '<input type="text" id="cable-sig-r" placeholder="siglatura dx" size="12"> <input type="text" id="cable-sig-l" placeholder="siglatura sx" size="12">'+
  '<img src="../wp-content/plugins/fmprevent-plugin/images/cavo_blu.png" >'+
  '<input type="text" id="cable-type" placeholder="tipo cavo" ></div>');

  FMPrevent.Templates.Connector=Handlebars.compile('<div class="conn conn-{{side}} conn-{{idx}}-of-{{n_conns}}" style="background-image:url(../wp-content/plugins/fmprevent-plugin/images/connettori/connettore_{{type}}_{{side}}.png)">'+
      '<input class="conn-label" type="text" placeholder="siglatura" value="{{label}}" size="12">'+
      '<select class="conn-selector" >'+
        '<option value="puntale" {{#if_eq type "puntale"}}selected{{/if_eq}}>Puntale</option>'+
        '<option value="faston" {{#if_eq type "faston"}}selected{{/if_eq}}>Faston</option>'+
        '<option value="occhiello" {{#if_eq type "occhiello"}}selected{{/if_eq}}>Occhiello</option>'+
        '<option value="forchetta" {{#if_eq type "forchetta"}}selected{{/if_eq}}>Forchetta</option>'+
      '</select>'+
    '</div>');

    FMPrevent.Templates.FreeCables=Handlebars.compile('<div class="free-cables free-cables-{{side}} free-cables-{{n_conns}}" style="background-image:url(../wp-content/plugins/fmprevent-plugin/images/cavi/{{n_conns}}_cavi_{{side}}.png)">'+
        '<input type="text" class="cable-sgua" placeholder="sguainatura" size="12">'+
        '<div class="conn-container">'+
        '</div>'+
        '</div>');

    FMPrevent.Models.CableEnd = Backbone.Model.extend({

       defaults:{

          side:'n',
          type:'n',
          n_conns:0,
          conns: new FMPrevent.Collections.Connectors({model:FMPrevent.Models.Connector})
      },

      initialize: function(){
        
        var conns=new FMPrevent.Collections.Connectors();
        for(i=0;i<this.get('n_conns');i++){
           
            conns.push(new FMPrevent.Models.Connector({idx:i+1,n_conns:this.get('n_conns'),side:this.get('side'),type:'puntale',label:''})); 
            
        }
        this.set('conns',conns);
    }

});

    FMPrevent.Models.Cable = Backbone.Model.extend({

      initialize: function(){

        var a=this.get('type').split('-');
        this.set('n_wires',parseInt(a[a.length-1]));
        this.set('right_end',new FMPrevent.Models.CableEnd({side:'r',type:'freecables',n_conns:this.get('n_wires')}));
        this.set('left_end',new FMPrevent.Models.CableEnd({side:'l',type:'freecables',n_conns:this.get('n_wires')}));

    }

   
});



    FMPrevent.Views.Connector = Backbone.View.extend({

        events:{

            "input .conn-label" : "change_label",
            "change .conn-selector" : "change_conn_type"
        },

        initialize: function(){

          _.bindAll(this, 'render');

        },

        render: function(){

            var html=FMPrevent.Templates.Connector(this.model.toJSON());
            this.$el.html(html);
            return this;
        },

        change_label: function(){

            this.model.set('label',this.$el.find('.conn-label').val());

        },

        change_conn_type: function(){

            this.model.set('type',this.$el.find('.conn-selector').val());
            this.render();

        }

    });


    FMPrevent.Views.CableEnd = Backbone.View.extend({

        events: {

          "input .cable-sgua"   : "change_end_sgua"

      },

      initialize: function(){

        _.bindAll(this, 'render');

      },

      render: function() {

        //var html = get_and_render('freecables',this.model.toJSON());
        var html=FMPrevent.Templates.FreeCables(this.model.toJSON());
        this.$el.html(html);
        var me=this.$el.find('div.conn-container');
        //_.each(this.model.get('conns'),function(el){
          for(i=0;i<this.model.get('conns').length;i++){
            var el=this.model.get('conns').at(i);
            var v=new FMPrevent.Views.Connector({model:el});
            me.append(v.render().$el);
          }
        //});
        return this;

    },

    change_end_sgua: function(){

        this.model.set('sgua',this.$el.find('.cable-sgua').val());

    }
});



    FMPrevent.Views.Cable = Backbone.View.extend({

      el: "#fmprevent",

      events: {
      "autocompleteselect #cable-type"   : "change_cable_model",
      "input #cable-sig-l"   : "change_label_l",
      "input #cable-sig-r"   : "change_label_r",
      "input #cable-length"   : "change_cable_length",

  },

  initialize: function(){

      this.render();
  },

  render: function() {

    //var html = get_and_render('cableview',null);
  this.$el.removeClass (function (index, css) {
    return (css.match (/\bfmprevent-\S+/g) || []).join(' ');
  });
  this.$el.addClass('fmprevent-'+this.model.get('n_wires'));
    var html=FMPrevent.Templates.Cable(this.model.toJSON());
    this.$el.html(html);
    var vr=new FMPrevent.Views.CableEnd({model:this.model.get('right_end')});
    this.$el.append(vr.render().$el);
    var vl=new FMPrevent.Views.CableEnd({model:this.model.get('left_end')});
    this.$el.append(vl.render().$el);
    var me=this;
    this.$el.find("#cable-type").autocomplete({
        source: cable_types
     }).val(this.model.get('type'));
},

change_cable_model:  function(e, ui){
    
    this.model=new FMPrevent.Models.Cable({type:ui.item.value});
    this.render();
},

change_label_l: function(){

    this.model.set('label_l',this.$el.find("#cable-sig-l").val());

},

change_label_r: function(){

    this.model.set('label_r',this.$el.find("#cable-sig-r").val());

},

change_cable_length: function(){

    this.model.set('t_length',this.$el.find("#cable-length").val());

},


});


})();