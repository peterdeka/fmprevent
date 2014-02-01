(function(){

FMPrevent={};
render={};
FMPrevent.Models={};
FMPrevent.Collections={};
FMPrevent.Views={};
FMPrevent.Models.Cable = Backbone.Model.extend();

FMPrevent.Models.Connector = Backbone.Model.extend();

FMPrevent.Models.CableEnd = Backbone.Model.extend();




FMPrevent.Views.CableEnd = Backbone.Views.extend({

	initialize: function(){
		var bkimg='images/cavi/'+this.model.n_conns+'_cavi_'+this.model.side+'.png';
		//this.render();
    },

    render: function() {

        var html = get_and_render('freecables',this.model.toJSON());
        return this.$el.html(html);
    }
});



FMPrevent.Views.Cable = Backbone.View.extend({

	 el: "#fmprevent",
	 right_end : new FMPrevent.Views.CableEnd({model:{side:'r',type:'freecables',n_conns:3,conns:[]}}),
	 left_end : new FMPrevent.Views.CableEnd({model:{side:'l',type:'freecables',n_conns:3,conns:[]}}),


     events: {
      "change #cable-type"   : "change_cable_model",
      
    },
    initialize: function(){
		
		this.render();
    },

    render: function() {

        var html = get_and_render('cableview',null);
        this.$el.html(html);
        this.$el.append(this.right_end.render());
    }
});


new FMPrevent.Views.Cable({model:{}});

})();







// And this is the definition of the custom function 
function get_and_render(tmpl_name, tmpl_data) {
    if ( !render.tmpl_cache ) { 
        render.tmpl_cache = {};
    }

    if ( ! render.tmpl_cache[tmpl_name] ) {
        var tmpl_dir = 'tpls';
        var tmpl_url = tmpl_dir + '/' + tmpl_name + '.html';

        var tmpl_string;
        $.ajax({
            url: tmpl_url,
            method: 'GET',
            async: false,
            success: function(data) {
                tmpl_string = data;
            }
        });

        render.tmpl_cache[tmpl_name] = Handlebars.compile(tmpl_string);
    }

    return render.tmpl_cache[tmpl_name](tmpl_data);
}