(function(){

FMPrevent={};
render={};
FMPrevent.Models={};
FMPrevent.Collections={};
FMPrevent.Views={};

FMPrevent.Models.CableEnd = Backbone.Model.extend({

	defaults:{

		side:'n',
		type:'n',
		n_conns:0,
		conns:[]
	}
});

FMPrevent.Models.Cable = Backbone.Model.extend({

		defaults:{

			type:'HEF3p',
			n_wires:3,
			right_end:new FMPrevent.Models.CableEnd({side:'r',type:'freecables',n_conns:3,conns:[]}),
			left_end:new FMPrevent.Models.CableEnd({side:'l',type:'freecables',n_conns:3,conns:[]})

		}

	});

FMPrevent.Models.Connector = Backbone.Model.extend();





FMPrevent.Views.CableEnd = Backbone.View.extend({

	initialize: function(){
		
		  
		//this.render();
    },

    render: function() {
    	
        var html = get_and_render('freecables',this.model.toJSON());
        return this.$el.html(html);
		
    }
});



FMPrevent.Views.Cable = Backbone.View.extend({

	 el: "#fmprevent",

     events: {
      "change #cable-type"   : "change_cable_model",
      
    },

    initialize: function(){
		
		this.render();
    },

    render: function() {

        var html = get_and_render('cableview',null);
        this.$el.html(html);
        this.$el.append(new FMPrevent.Views.CableEnd({model:this.model.get('right_end')}).render());
        this.$el.append(new FMPrevent.Views.CableEnd({model:this.model.get('left_end')}).render());
    }
});


new FMPrevent.Views.Cable({model:new FMPrevent.Models.Cable()});

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