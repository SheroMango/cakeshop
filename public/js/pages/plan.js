////////////////////模型////////////////////
var DayModelClass = Backbone.Model.extend({
    urlRoot: 'index.php?g=Rest&m=PlanDay&plan='+planId,
    url: function(){
        var base = _.result(this, 'urlRoot') || urlError();
        if (this.isNew()) {
            return base;
        }
        return base+'&id='+this.id;
    }
});

//地点模型类
PlaceModelClass = Backbone.Model.extend({
    urlRoot: 'index.php?g=Rest&m=PlanPlace&plan='+planId,
    url: function(){
        var base = _.result(this, 'urlRoot') || urlError();
        if (this.isNew()) {
            return base;
        }
        return base+'&id='+this.id;
    }
});

//备注模型类
RemarkModelClass = Backbone.Model.extend({
    urlRoot: 'index.php?g=Rest&m=PlanRemark&plan='+planId,
    url: function(){
        var base = _.result(this, 'urlRoot') || urlError();
        if (this.isNew()) {
            return base;
        }
        return base+'&id='+this.id;
    }
});

//地点模型类
CostModelClass = Backbone.Model.extend({
    urlRoot: 'index.php?g=Rest&m=PlanCost&plan='+planId,
    url: function(){
        var base = _.result(this, 'urlRoot') || urlError();
        if (this.isNew()) {
            return base;
        }
        return base+'&id='+this.id;
    }
});

//高级选择器地点模型
AdvancedPlaceModelClass = Backbone.Model.extend({
    urlRoot: 'index.php?g=Rest&m=Place'
});

////////////////////集合////////////////////
//日程集合
var DayCollectionClass = Backbone.Collection.extend({
    model: DayModelClass,
    url: 'index.php?g=Rest&m=PlanDay&plan='+planId
});
var dayCollectionObj = new DayCollectionClass();
dayCollectionObj.set(defaultDayList);

//地点集合类
PlaceCollectionClass = Backbone.Collection.extend({
    model: PlaceModelClass
});
var placeCollectionObj = new PlaceCollectionClass();
placeCollectionObj.set(defaultPlaceList);

//备注集合类
RemarkCollectionClass = Backbone.Collection.extend({
    model: RemarkModelClass
});
var remarkCollectionObj = new RemarkCollectionClass();
remarkCollectionObj.set(defaultRemarkList);

//预算集合类
CostCollectionClass = Backbone.Collection.extend({
    model: CostModelClass
});
var costCollectionObj = new CostCollectionClass();
costCollectionObj.set(defaultCostList);

//高级选择器地点集合
AdvancedPlaceCollectionClass = Backbone.Collection.extend({
    url: 'index.php?g=Rest&m=Place',
    model: AdvancedPlaceModelClass
});
var advancedPlaceCollectionObj = new AdvancedPlaceCollectionClass();

////////////////////视图////////////////////

//////////备注相关视图//////////

//日程编辑视图
var DayEditViewClass = Backbone.View.extend({
    //标签名
    tagName: 'div',
    //模板
    template: _.template($('#day-edit-template').html()),
    events: {
        'click #day-edit-cancel': 'cancel',
        'click #day-edit-submit': 'submit'
    },
    //初始化
    initialize: function() {
        this.render();
    },
    //渲染模板
    render: function() {
        var type = this.options.type;
        var siblingId = this.options.siblingId || 0;
        var data = {
            header: '',
            type: type,
            siblingId: siblingId,
            title: '',
            content: ''
        };
        //判断操作类型：添加、插入、修改
        if (type == 'create') {
            data.header = '添加日程';
        } else if (type == 'insert') {
            data.header = '插入日程';
        } else {
            data.header = '修改日程';
            data = _.extend(data, this.model.toJSON());
        }

        this.$el.html(this.template(data));
        var content = this.$el;
        self = this;
        $.fancybox.open({
            padding: 0,
            autoSize: true,
            minWidth: 300,
            content: content,
            afterClose: function(){
                self.remove();
            }
        });
        return this;
    },
    //提交
    submit: function() {
        var title = $('#day-edit-title').val();
        var type = $('#day-edit-type').val();
        var siblingId = $('#day-edit-sibling').val();
        if (!title) {
            alert('日程标题不能为空');
            return false;
        }
        //
        self = this;
        if (type == 'create' || type == 'insert') {
            this.model = new DayModelClass({
                title: title,
            });
            this.model.save(null, {
                success:function(){
                    dayCollectionObj.add(self.model);
                    appViewObj.renderDay(self.model, siblingId);
                    //appViewObj.resetOrder();
                }
            });
        } else {
            //修改章节
            this.model.set({
                title: title
            });
            this.model.save();
        }
        this.cancel();
    },
    //取消
    cancel: function() {
        $.fancybox.close();
    }

});

//日程标题视图
var DayNavViewClass = Backbone.View.extend({
    //id
    id: function(){
        return 'day-nav-'+this.model.id;
    },
    //标签名
    tagName: 'li',
    //模板
    template: _.template($('#day-nav-item-template').html()),
    //事件
    events: {
        'click .edit-day': 'editDay',
        'click .insert-day': 'insertDay',
        'click .del-day' : 'destroy',
        'click .up-day' : 'up',
        'click .down-day' : 'down'
    },
    //初始化
    initialize: function() {
        this.listenTo(this.model, 'change', this.update);
        this.listenTo(this.model, 'destroy', this.remove);
    },
    //渲染视图
    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    //修改
    update: function() {
        this.$el.find('.day-title dt').text(this.model.get('title'));
    },
    //编辑章节
    editDay: function() {
        var editViewObj = new DayEditViewClass({type: 'update', model: this.model});
    },
    //插入日程
    insertDay: function() {
        var editViewObj = new DayEditViewClass({type: 'insert', siblingId: this.model.id});
    },
    //向上移动
    up: function (){
       var srcIndex = chapterNav.children().index(this.$el);
       var destIndex = srcIndex - 1;
       chapterAppViewObj.move(srcIndex, destIndex);
    },
    //向下移动
    down: function(){
       var srcIndex = chapterNav.children().index(this.$el);
       var destIndex = srcIndex + 1;
       chapterAppViewObj.move(srcIndex, destIndex);
    },
    //删除模型
    destroy: function() {
        this.model.destroy();
    }
})

 //日程详情视图
var DayContentViewClass = Backbone.View.extend({
    //id
    id: function(){
        return 'day-content-'+this.model.id;
    },
    //标签名
    tagName: 'div',
    //类名
    className: 'plan_day_content',
    //模板
    template: _.template($('#day-content-item-template').html()),
    //事件
    events: {
        'click .add-remark': 'addRemark',
        'click .add-cost': 'addCost',
        'click .show-place-add-fast': 'showFastPlaceAdd',
        'click .show-place-add-advanced': 'showAdvancedPlaceAdd',
    },
    //初始化
    initialize: function(){
        this.listenTo(this.model, 'change', this.render);
        this.listenTo(this.model, 'destroy', this.remove);
    },
    //渲染视图
    render: function() {
        var self = this;
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    //删除模型
    destroy: function() {
        this.model.destroy();
    },
    //快速添加地点
    showFastPlaceAdd: function(){
        var fastPlaceAddViewObj = new FastPlaceAddViewClass({model: this.model});
    },
    //高级地点添加
    showAdvancedPlaceAdd: function(){
        var advancedPlaceAddViewObj = new AdvancedPlaceAddViewClass({model: this.model});
    },
    //重排地点顺序
    resetPlaceOrder: function(){
        var orderStr = this.$el.find('.chapter-place-list').sortable('serialize');
        orderStr = orderStr.replace(/chapter-place/g, 'order');
        var url = 'index.php?g=Rest&m=StrategyPlace&a=order';
        url += '&'+orderStr;
        $.get(url, function(data){
           //
        });
    },
    //添加地点备注
    addRemark: function() {
        var remarkEditViewObj = new RemarkEditViewClass({
            type: 'create',
            day: this.model.get('id')
        });
    },
    //添加地点花费
    addCost: function() {
        var costEditViewObj = new CostEditViewClass({
            type: 'create',
            day: this.model.get('id')
        });
    }
});


//////////地点相关视图//////////

//快速地点添加器
var FastPlaceAddViewClass = Backbone.View.extend({
    //标签名
    tagName: 'div',
    //模板
    template: _.template($('#place-add-fast-template').html()),
    events: {
        'click #place-add-cancel': 'cancel',
        'click #place-add-submit': 'submit'
    },
    //初始化
    initialize: function() {
        this.render();
    },
    //渲染模板
    render: function() {
        self = this;
        this.$el.html(this.template());
        //搜索提示
        this.$el.find('#place-name').autocomplete({
            source: 'index.php?g=Rest&m=Place&a=autocomplete',
            appendTo: this.$el.find('#autocomplete-result'),
            select: function(e, ui) {
                var place = {
                    day_id: self.model.id,
                    place_id:ui.item.pid
                };
                var placeModelObj = new PlaceModelClass(place);
                placeModelObj.save(null, {
                    success:function(){
                        console.log(placeModelObj);
                        placeCollectionObj.add(placeModelObj);
                        appViewObj.renderPlace(placeModelObj);
                        self.cancel();
                    }
                });
            }
        });
        $.fancybox.open({
            autoSize: true,
            minWidth: 680,
            minHeight: 450,
            content: this.$el,
            afterClose: function(){
                self.remove();
            }
        });
        return this;
    },
    //取消
    cancel: function() {
        $.fancybox.close();
    }

});

//高级地点添加器
var AdvancedPlaceAddViewClass = Backbone.View.extend({
    tagName: 'div',
    template: _.template($('#place-add-advanced-template').html()),
    events: {
        'click #advanced-place-search-btn': 'searchPlace'
    },
    initialize: function() {
        this.render();
        this.searchPlace();
    },
    render: function() {
        self = this;
        this.$el.html(this.template());
        $.fancybox.open({
            autoSize: true,
            minWidth: 660, 
            minHeight: 500,
            content: self.$el
        });
    },
    renderPlace: function(){
        var self = this;
        this.$el.find('.advanced-place-list').html('');
        _.each(advancedPlaceCollectionObj.models, function(place){
            place.set('day_id', self.model.id);
            var advancedPlaceViewObj =  new AdvancedPlaceViewClass({model: place});
            self.$el.find('.advanced-place-list').append(advancedPlaceViewObj.$el);
        });
    },
    //地点搜索
    searchPlace: function(){
        var self = this;
        var data = {};
        //类型
        var ptype = this.$el.find("input:checked").val();
        if (ptype) {
            data.ptype = ptype;
        }
        //关键字
        var pkeyword = this.$el.find('#advanced-place-search-keyword').val();
        if (pkeyword) {
            data.pkeyword = pkeyword;
        }
        advancedPlaceCollectionObj.fetch({
            data: data,
            reset: true,
            success: function(collection){
                self.renderPlace();
            }
        });

    },
    //取消
    cancel: function() {

    }   
});

//高级添加器中的地点
var AdvancedPlaceViewClass = Backbone.View.extend({
    tagName: 'dl',
    template: _.template($('#advanced-place-item-template').html()),
    events: {
        'click .add-to-plan': 'addToPlan'
    },
    initialize: function(){
        this.render();
    },
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    addToPlan: function(){
        var place = {
            place_id: this.model.get('id'),
            day_id: this.model.get('day_id'),
        };
        var placeModelObj = new PlaceModelClass(place);
        placeModelObj.save(null, {
            success:function(){
                placeCollectionObj.add(placeModelObj);
                appViewObj.renderPlace(placeModelObj);
                $.fancybox.close();
            }
        });
    }
});

//地点视图
var PlaceViewClass = Backbone.View.extend({
    id: function(){
        return 'day-place-'+this.model.id;
    },
    tagName: 'dl',
    template: _.template($('#day-place-item-template').html()),
    events: {
        'click .del-place' : 'destroy'
    },
    initialize: function(){
        this.listenTo(this.model, 'destroy', this.remove);
        this.render();
    },
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    //删除
    destroy: function(){
        this.model.destroy();
    }
});


//////////备注相关视图//////////

//日程备注添加、编辑
var RemarkEditViewClass = Backbone.View.extend({
    //标签名
    tagName: 'div',
    //模板
    template: _.template($('#day-remark-edit-template').html()),
    events: {
        'click #day-remark-edit-cancel': 'cancel',
        'click #day-remark-edit-submit': 'submit'
    },
    //初始化
    initialize: function() {
        this.render();
    },
    //渲染模板
    render: function() {
        var type = this.options.type;
        var day = this.options.day;
        var data = {
            header: '',
            type: type,
            day: day,
            content: ''
        };
        //判断操作类型：添加、插入、修改
        if (type == 'create') {
            data.header = '添加备注';
        } else {
            data.header = '修改备注';
            data.day = this.model.get('day_id');
            data.content = this.model.get('content');
        }

        this.$el.html(this.template(data));
        var content = this.$el;
        self = this;
        $.fancybox.open({
            padding: 0,
            autoSize: true,
            content: content,
            afterClose: function(){
                self.remove();
            }
        });
        return this;
    },
    //提交
    submit: function() {
        var content = $('#day-remark-edit-content').val();
        var day = $('#day-remark-edit-day').val();
        var type = $('#day-remark-edit-type').val();
        self = this;
        if (type == 'create') {
            //添加备注
            this.model = new RemarkModelClass({
                day_id: day,
                content: content
            });
            this.model.save(null, {
                success:function(){
                    remarkCollectionObj.add(self.model);
                    appViewObj.renderRemark(self.model);
                }
            });
        } else {
            //修改备注
            this.model.set({
                content: content
            });
            this.model.save();
        }
        this.cancel();
    },
    //取消
    cancel: function() {
        $.fancybox.close();
    }

});

//日程备注
var RemarkViewClass = Backbone.View.extend({
    id: function(){
        return 'day-remark-'+this.model.id;
    },
    tagName: 'p',
    template: _.template($('#day-remark-item-template').html()),
    events: {
        'click .edit-day-remark' : 'edit'
    },
    initialize: function(){
        this.render();
        this.listenTo(this.model, 'change', this.render);
    },
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    edit: function() {
        var remarkEditViewObj = new RemarkEditViewClass({type: 'update', model: this.model});
    }
});


//////////预算相关视图//////////

//添加、编辑花费
var CostEditViewClass = Backbone.View.extend({
    //标签名
    tagName: 'div',
    //模板
    template: _.template($('#day-cost-edit-template').html()),
    events: {
        'click #day-cost-edit-cancel': 'cancel',
        'click #day-cost-edit-submit': 'submit'
    },
    //初始化
    initialize: function() {
        this.render();
    },
    //渲染模板
    render: function() {
        var type = this.options.type;
        var day = this.options.day;
        var data = {
            header: '',
            type: type,
            day: day,
            title: '',
            amount: '',
        };
        if (type == 'create') {
            data.header = '添加预算';
        } else {
            data.header = '修改预算';
            data.day = this.model.get('day_id');
            data.title = this.model.get('title');
            data.amount = this.model.get('amount');
            data.currency = this.model.get('currency');
        }
        this.$el.html(this.template(data));
        this.$el.find('#day-cost-edit-currency').val(data.currency);
        var content = this.$el;
        self = this;
        $.fancybox.open({
            padding: 0,
            autoSize: true,
            content: content,
            afterClose: function(){
                self.remove();
            }
        });
        return this;
    },
    //提交
    submit: function() {
        var day = $('#day-cost-edit-day').val();
        var type = $('#day-cost-edit-type').val();
        var title = $('#day-cost-edit-title').val();
        var amount = $('#day-cost-edit-amount').val();
        var currency = $('#day-cost-edit-currency').val();

        self = this;
        if (type == 'create') {
            //添加备注
            this.model = new CostModelClass({
                day_id: day,
                title: title,
                amount: amount,
                currency: currency
            });
            this.model.save(null, {
                success:function(){
                    costCollectionObj.add(self.model);
                    appViewObj.renderCost(self.model);
                }
            });
        } else {
            //修改备注
            this.model.set({
                title: title,
                amount: amount,
                currency: currency
            });
            this.model.save();
        }
        this.cancel();
    },
    //取消
    cancel: function() {
        $.fancybox.close();
    }

});

//花费条目
var CostViewClass = Backbone.View.extend({
    id: function(){
        return 'day-cost-'+this.model.id;
    },
    tagName: 'tr',
    template: _.template($('#day-cost-item-template').html()),
    events: {
        'click .edit-cost': 'edit',
        'click .del-cost' : 'destroy'
    },
    initialize: function(){
        this.listenTo(this.model, 'change', this.render);
        this.listenTo(this.model, 'destroy', this.remove);
        this.render();
    },
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    //编辑
    edit: function() {
        var costEditViewObj = new CostEditViewClass({type: 'update', model: this.model});
    },
    //删除
    destroy: function(){
        alert('destroy...');
        this.model.destroy();
    }
});

////////////////////应用视图////////////////////
//应用视图
var AppViewClass = Backbone.View.extend({
    //元素
    el: $('#days'),
    //事件
    events: {
        'click #show-day-edit': 'showDayEdit'
    },

    //初始化
    initialize: function() {
        this.render();
    },

    //显示添加表单
    showDayEdit: function() {
        var dayEditViewObj = new DayEditViewClass({type: 'create'});
    },

    //渲染应用视图
    render: function() {
        //渲染日程
        _.each(dayCollectionObj.models, function(item){
            this.renderDay(item);
        }, this);

        //渲染地点
        _.each(placeCollectionObj.models, function(item){
            this.renderPlace(item);
        }, this);

        //渲染备注
        _.each(remarkCollectionObj.models, function(item){
            this.renderRemark(item);
        }, this);

        //渲染预算
        _.each(costCollectionObj.models, function(item){
            this.renderCost(item);
        }, this);

    },

    //渲染日程
    renderDay: function(dayModelObj, siblingId) {
        this.$el.find('#no-day-title').remove();
        var navViewObj = new DayNavViewClass({
            model: dayModelObj
        });
        var contentViewObj = new DayContentViewClass({
            model: dayModelObj
        });

        if (siblingId && siblingId != 0) {
            $('#day-nav-'+siblingId).after(navViewObj.render().$el);
            $('#day-content-'+siblingId).after(contentViewObj.render().$el);
        } else {
            this.$el.find('#day-nav').append(navViewObj.render().$el);
            this.$el.find('#day-content').append(contentViewObj.render().$el);
        }
    },

    //渲染地点
    renderPlace: function(placeModelObj) {
        var placeViewObj = new PlaceViewClass({
            model: placeModelObj
        });
        placeViewObj.$el.insertBefore('#day-content-'+placeModelObj.get('day_id')+' .place-add-wrap');
    },
    //渲染备注
    renderRemark: function (remarkModelObj) {
        var remarkViewObj = new RemarkViewClass({
            model: remarkModelObj
        });
        $('#day-content-'+remarkModelObj.get('day_id')+' .day_remark').html(remarkViewObj.$el);
    },
    //渲染预算
    renderCost: function (costModelObj) {
        var costViewObj = new CostViewClass({
            model: costModelObj
        });
        $('#day-content-'+costModelObj.get('day_id')+' .day-cost-list' ).append(costViewObj.$el);
    }
});

//实例化章节视图
var appViewObj = new AppViewClass();

