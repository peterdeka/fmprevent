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

   FMPrevent.Templates.Cable=Handlebars.compile('<div id="cable-body" ><div id="cable-length">Lunghezza(mm):<input type="text" placeholder="lunghezza" value="{{t_length}}">'+
    '<img src="'+ajax_object.siteurl+'/wp-content/plugins/fmprevent-plugin/images/lunghezza_cavo.png"></div>'+
      '<input type="text" id="cable-sig-r" placeholder="siglatura dx" size="12" value="{{label_r}}"> <input type="text" id="cable-sig-l" placeholder="siglatura sx" size="12" value="{{label_l}}">'+
  '<img src="'+ajax_object.siteurl+'/wp-content/plugins/fmprevent-plugin/images/cavo_blu.png" >'+
  '<input type="text" id="cable-type" placeholder="tipo cavo" value="{{type}}"></div>');

  FMPrevent.Templates.Connector=Handlebars.compile('<div class="conn conn-{{side}} conn-{{idx}}-of-{{n_conns}}" style="background-image:url('+ajax_object.siteurl+'/wp-content/plugins/fmprevent-plugin/images/connettori/connettore_{{nome}}_{{side}}.png)">'+
      '<input class="conn-label" type="text" placeholder="siglatura" value="{{label}}" size="12">'+
      '<select class="conn-selector" >'+
        '{{#each conntypes}}<option value="{{@index}}" {{#if_eq ../tipo this.id}}selected{{/if_eq}}>{{this.nome}}</option>{{/each}}'+
        
      '</select>'+

      '<div class="conn-size-selector"><select>'+
       '{{#each sel_sizes}}<option value="{{@index}}" {{#if_eq ../size this.id}}selected{{/if_eq}}>{{this.size}}</option>{{/each}}'+
      '</select>'+
      '<p>mm</p></div>'+
    '</div>');


    FMPrevent.Templates.FreeCables=Handlebars.compile('<div class="free-cables free-cables-{{side}} free-cables-{{n_conns}}" style="background-image:url('+ajax_object.siteurl+'/wp-content/plugins/fmprevent-plugin/images/cavi/{{n_conns}}_cavi_{{side}}.png)">'+
        '<div class="cable-sgua"><input type="text"  placeholder="sguainatura" size="12" value="{{sgua}}"><p>Sguainatura(mm)</p></div>'+
        '<div class="conn-container">'+
        '</div>'+
        '</div>');

    FMPrevent.Templates.OrderInfo=Handlebars.compile('<div style="font-size:15px" id="orderdetails"><p>ID ordine: {{id}}  -  Quantità: {{quantity}} - Nome: {{name}}  - Tel.: {{phone}}  - email: {{email}}  -  Messaggio: {{msg}}</p></div>');

    FMPrevent.Models.CableEnd = Backbone.Model.extend({

       defaults:{

          side:'n',
          type:'n',
          n_conns:0
      },

      initialize: function(){
        
        if(this.get('conns') )
          return;
        var conns=new FMPrevent.Collections.Connectors();
        var me=this;

        for(i=0;i<this.get('n_conns');i++){
            
        var m = jQuery.extend(true,{},conn_type);
		        m['idx']=i+1;
            m['n_conns']=me.get('n_conns');
            m['side']=me.get('side');
            m['label']='';
            
            conns.push(new FMPrevent.Models.Connector(m)); 
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
        this.set('label_l','');
        this.set('label_r','');
        this.set('t_length',0);

    },

    loadJSON: function(jso){

      this.set('type',jso.type);
      this.set('n_wires',jso.n_wires);
      this.set('label_l',jso.label_l);
      this.set('label_r',jso.label_r);
      this.set('t_length',jso.t_length);
      
      var jsor=jso.right_end;
      _.each(jsor.conns,function(el,i){
          jsor.conns[i]= new FMPrevent.Models.Connector(el);

      });
      this.set('right_end',new FMPrevent.Models.CableEnd({side:'r',type:jsor.type,n_conns:jsor.n_conns,sgua:jsor.sgua,conns:new FMPrevent.Collections.Connectors(jsor.conns)}));
      var jsor=jso.left_end;
     _.each(jsor.conns,function(el,i){
          jsor.conns[i]= new FMPrevent.Models.Connector(el);

      });
      this.set('left_end',new FMPrevent.Models.CableEnd({side:'l',type:jsor.type,n_conns:jsor.n_conns,sgua:jsor.sgua,conns:new FMPrevent.Collections.Connectors(jsor.conns)}));

    },

    gen_distinta: function(){

      var html='<table style="border:1px solid #666;width:700px;background-color:white"><tr><th>Componente</th><th>Unita</th><th>Prezzo-u</th><th>Prezzo totale</th>';
      var tidx=cable_types.indexOf(this.get('type'));
      var tl=this.get('t_length');
      var rend=this.get('right_end').get('conns').models;
      var lend=this.get('left_end').get('conns').models;
      var nconns=rend[0].get('n_conns');
      var total=tl/1000*cable_prices[tidx];
      html+='<tr><td>Cavo. '+this.get('type')+'</td><td>'+tl+'</td><td>'+cable_prices[tidx]+'</td><td>'+total+'</td></tr>';
      var group_conns={};
      for(var i=0;i<nconns;i++){
        //conto raggruppati
        if(!(rend[i].get('codice') in group_conns)){
          group_conns[rend[i].get('codice') ]={c:1,prc:rend[i].get('price')};
        }
        else
          group_conns[rend[i].get('codice') ].c+=1

        if(!(lend[i].get('codice') in group_conns)){
          group_conns[lend[i].get('codice') ]={c:1,prc:lend[i].get('price')};;
        }
        else
          group_conns[lend[i].get('codice') ].c+=1
      }
    
      jQuery.each(group_conns,function(i,el){
        total+=el.c*el.prc;
        html+='<tr><td>Conn. '+i+'</td><td>'+el.c+'</td><td>'+el.prc+'</td><td>'+el.c*el.prc+'</td></tr>';
      });
      html+='<tr style="border-top:1px solid #666"><td>Totale</td><td></td><td></td><td>'+total+'</td></tr>';
      return html+'</table>';


    }

   
});



    FMPrevent.Views.Connector = Backbone.View.extend({

        events:{

            "input .conn-label" : "change_label",
            "change .conn-selector" : "change_conn_type",
            "change .conn-size-selector" : "change_conn_size"
        },

        initialize: function(){

          _.bindAll(this, 'render');
        
        },

        render: function(){
          
            if (typeof(this.model.get('sel_sizes'))=='undefined'){  //defaults
              var ct=this.model.get('conntypes')[0];
              this.model.set('nome',ct.nome);
              this.model.set('tipo',ct.id);
              this.model.set('sel_sizes',ct.sizes);
              this.model.set('size',ct.sizes[0].id);
              this.model.set('price',ct.sizes[0].prezzo);
              this.model.set('codice',ct.sizes[0].codice);
            }
            var html=FMPrevent.Templates.Connector(this.model.toJSON());
            this.$el.html(html);
          
            return this;
        },

        change_label: function(){

            this.model.set('label',this.$el.find('.conn-label').val());

        },

        change_conn_type: function(){

	         var newidx=this.$el.find('.conn-selector').val();
	         var ct=this.model.get('conntypes')[newidx];
           this.model.set('nome',ct.nome);
           this.model.set('tipo',ct.id);
           this.model.set('sel_sizes',ct.sizes);
	         this.model.set('size',ct.sizes[0].id);
           this.model.set('price',ct.sizes[0].prezzo);
            this.model.set('codice',ct.sizes[0].codice);
	         this.render();

        },

        change_conn_size: function(){
            var sz=this.$el.find('.conn-size-selector select').val();
            this.model.set('size',this.model.get('sel_sizes')[sz].id);
            this.model.set('price',this.model.get('sel_sizes')[sz].prezzo);
            this.model.set('codice',this.model.get('sel_sizes')[sz].codice);

        }

    });


    FMPrevent.Views.CableEnd = Backbone.View.extend({

        events: {

          "input .cable-sgua input"   : "change_end_sgua"

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

        this.model.set('sgua',this.$el.find('.cable-sgua input').val());

    }
});



    FMPrevent.Views.Cable = Backbone.View.extend({

      el: "#fmprevent",

      events: {
      "autocompleteselect #cable-type"   : "change_cable_model",
      "input #cable-sig-l"   : "change_label_l",
      "input #cable-sig-r"   : "change_label_r",
      "input #cable-length input"   : "change_cable_length",

  },

  initialize: function(){
      this.$el.html('');
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
    if('undefined' == typeof loadingctx){
    this.$el.find("#cable-type").autocomplete({
        source: cable_types
     }).val(this.model.get('type'));
    }
},

change_cable_model:  function(e, ui){
    
    this.model=new FMPrevent.Models.Cable({type:ui.item.value});
    pricefield=new FMPrevent.Views.Price({model:this.model});
    this.render();
},

change_label_l: function(){

    this.model.set('label_l',this.$el.find("#cable-sig-l").val());

},

change_label_r: function(){

    this.model.set('label_r',this.$el.find("#cable-sig-r").val());

},

change_cable_length: function(){

    this.model.set('t_length',this.$el.find("#cable-length input").val());

}


});

FMPrevent.Views.Price = Backbone.View.extend({

    el:'#priceview',

  initialize: function(){

      this.listenTo(this.model, "change", this.render);
      this.listenTo(this.model.get('right_end'), "change", this.render);
      this.listenTo(this.model.get('left_end'), "change", this.render);
      this.listenTo(this.model.get('left_end').get('conns'), "change", this.render);
      this.listenTo(this.model.get('right_end').get('conns'), "change", this.render);
      this.render();

  },

  render: function() {
    
    var tidx=cable_types.indexOf(this.model.get('type'));
    var tl=this.model.get('t_length');
    var rend=this.model.get('right_end').get('conns').models;
    var lend=this.model.get('left_end').get('conns').models;
    var nconns=rend[0].get('n_conns');
    var total=tl/1000*cable_prices[tidx];
    for(var i=0;i<nconns;i++){

      total+=rend[i].get('price');
      total+=lend[i].get('price');

    }
    this.$el.find('#est_price').html(total);

  }
});


})();