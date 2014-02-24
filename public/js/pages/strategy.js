////////////////////模型////////////////////
//章节模型类
var ChapterModelClass = Backbone.Model.extend({
    urlRoot: 'index.php?g=Rest&m=StrategyChapter&strategy='+strategyId,
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
    urlRoot: 'index.php?g=Rest&m=StrategyPlace&strategy='+strategyId,
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
//章节集合
var ChapterCollectionClass = Backbone.Collection.extend({
    model: ChapterModelClass,
    url: 'index.php?g=Rest&m=StrategyChapter&strategy='+strategyId
});
var chapterCollectionObj = new ChapterCollectionClass();
chapterCollectionObj.set(defaultChapters);

//地点集合类
PlaceCollectionClass = Backbone.Collection.extend({
    model: PlaceModelClass
});
var placeCollectionObj = new PlaceCollectionClass();
placeCollectionObj.set(defaultPlaces);

//高级选择器地点集合
AdvancedPlaceCollectionClass = Backbone.Collection.extend({
    url: 'index.php?g=Rest&m=Place',
    model: AdvancedPlaceModelClass
});
var advancedPlaceCollectionObj = new AdvancedPlaceCollectionClass();


////////////////////视图////////////////////
//章节导航视图
var ChapterNavViewClass = Backbone.View.extend({
    //id
    id: function(){
        return 'chapter-nav-'+this.model.id;
    },
    //标签名
    tagName: 'li',
    //模板
    template: _.template($('#chapter-nav-item-template').html()),
    //事件
    events: {
        'click .edit-chapter': 'editChapter',
        'click .del-chapter' : 'destroy',
        'click .up-chapter' : 'up',
        'click .down-chapter' : 'down'
    },
    //初始化
    initialize: function() {
        this.listenTo(this.model, 'change', this.render);
        this.listenTo(this.model, 'destroy', this.remove);
    },
    //渲染视图
    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        //渲染地点

        return this;
    },
    //编辑章节
    editChapter: function() {
        var editViewObj = new ChapterEditViewClass({type: 'update', model: this.model});
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

//章节内容视图
var ChapterContentViewClass = Backbone.View.extend({
    //id
    id: function(){
        return 'chapter-content-'+this.model.id;
    },
    //标签名
    tagName: 'div',
    //类名
    className: 'right_chapters_content',
    //模板
    template: _.template($('#chapter-content-item-template').html()),
    //事件
    events: {
        'click .edit-chapter': 'editChapter',
        'click .insert-chapter': 'insertChapter',
        'click .del-chapter' : 'destroy',
        'click .up-chapter' : 'up',
        'click .down-chapter' : 'down',
        'click .show-place-add-fast' : 'showFastPlaceAdd',
        'click .show-place-add-advanced' : 'showAdvancedPlaceAdd',
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
        this.$el.find('.chapter-place-list').sortable({
            stop: function(){
                self.resetPlaceOrder();
            }
        });
        return this;
    },
    //编辑章节
    editChapter: function() {
        var editViewObj = new ChapterEditViewClass({type: 'update', model: this.model});
    },
    //插入章节
    insertChapter: function() {
        var editViewObj = new ChapterEditViewClass({type: 'insert', siblingId: this.model.id});
    },
    //上移
    up: function(){
       var srcIndex = chapterContent.children().index(this.$el);
       var destIndex = srcIndex - 1;
       chapterAppViewObj.move(srcIndex, destIndex);
    },
    down: function(){
       var srcIndex = chapterContent.children().index(this.$el);
       var destIndex = srcIndex + 1;
       chapterAppViewObj.move(srcIndex, destIndex);
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
    }
});

//章节编辑视图
var ChapterEditViewClass = Backbone.View.extend({
    //标签名
    tagName: 'div',
    //模板
    template: _.template($('#chapter-edit-template').html()),
    events: {
        'click #chapter-edit-cancel': 'cancel',
        'click #chapter-edit-submit': 'submit'
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
        if (type == 'create') {//添加新章节
            data.header = '添加新章节';
        } else if (type == 'insert') {//插入章节
            data.header = '插入新章节';
        } else {//修改章节
            data = _.extend(data, this.model.toJSON());
            data.header = '编辑章节';
        }
        this.$el.html(this.template(data));
        var content = this.$el;
        self = this;
        $.fancybox.open({
            autoSize: true,
            padding: 0,
            content: content,
            afterClose: function(){
                self.remove();
            }
        });
        return this;
    },
    //提交
    submit: function() {
        var title = $('#chapter-edit-title').val();
        var content = $('#chapter-edit-content').val();
        var type = $('#chapter-edit-type').val();
        var siblingId = $('#chapter-edit-sibling').val();
        if (!title) {
            alert('请填写章节标题');
            return false;
        }
        if (!content) {
            alert('请填写章节内容');
            return false;
        }
        //
        self = this;
        if (type == 'create' || type == 'insert') {
            this.model = new ChapterModelClass({
                title: title,
                content: content
            });
            this.model.save(null, {
                success:function(){
                    chapterCollectionObj.add(self.model);
                    chapterAppViewObj.renderChapter(self.model, siblingId);
                    chapterAppViewObj.resetOrder();
                }
            });
        } else {
            //修改章节
            this.model.set({
                title: title,
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

//章节地点视图
var PlaceViewClass = Backbone.View.extend({
    id: function(){
        return 'chapter-place-'+this.model.id;
    },
    tagName: 'dl',
    template: _.template($('#chapter-place-item-template').html()),
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

//地点快速添加器
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
                    chapter_id: self.model.id,
                    place_id:ui.item.pid,
                };
                var placeModelObj = new PlaceModelClass(place);
                placeModelObj.save(null, {
                    success:function(){
                        placeCollectionObj.add(placeModelObj);
                        chapterAppViewObj.renderPlace(placeModelObj);
                        self.cancel();
                    }
                });
            }
        });
        $.fancybox.open({
            autoSize: true,
            padding: 0,
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

//地点高级添加器
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
            padding:0,
            content: self.$el
        });
    },
    renderPlace: function(){
        var self = this;
        this.$el.find('.advanced-place-list').html('');
        _.each(advancedPlaceCollectionObj.models, function(place){
            place.set('chapter_id', self.model.id);
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

//高级添加器
var AdvancedPlaceViewClass = Backbone.View.extend({
    tagName: 'dl',
    template: _.template($('#advanced-place-item-template').html()),
    events: {
        'click .add-to-strategy': 'addToStrategy'
    },
    initialize: function(){
        this.render();
    },
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    addToStrategy: function(){
        var place = {
            place_id: this.model.get('id'),
            chapter_id: this.model.get('chapter_id'),
        };
        var placeModelObj = new PlaceModelClass(place);
        placeModelObj.save(null, {
            success:function(){
                placeCollectionObj.add(placeModelObj);
                chapterAppViewObj.renderPlace(placeModelObj);
                $.fancybox.close();
            }
        });
    }
});


////////////////////应用视图////////////////////
//应用视图
var ChapterAppViewClass = Backbone.View.extend({
    //元素
    el: $('#chapter'),
    //事件
    events: {
        'click #show-chapter-edit': 'showChapterEdit'
    },
    //初始化
    initialize: function() {
        this.render();
    },
    //渲染视图
    render: function() {
        
        //渲染所有章节视图
        _.each(chapterCollectionObj.models, function(item){
            this.renderChapter(item);
        }, this);

        //渲染所有地点视图
        _.each(placeCollectionObj.models, function(item){
            this.renderPlace(item);
        }, this);
    },

    //章节视图
    renderChapter: function(chapterModelObj, siblingId) {
        this.$el.find('#no-chapter-nav').remove();
        var navView = new ChapterNavViewClass({
            model: chapterModelObj
        });
        var contentView = new ChapterContentViewClass({
            model: chapterModelObj
        });
        if (siblingId && siblingId != 0) {
            $('#chapter-nav-'+siblingId).after(navView.render().$el);
            $('#chapter-content-'+siblingId).after(contentView.render().$el);
        } else {
            this.$el.find('#chapter-nav').append(navView.render().$el);
            this.$el.find('#chapter-content').append(contentView.render().$el);
        }
    },
    //地点视图
    renderPlace: function(placeModelObj){
        var placeViewObj = new PlaceViewClass({
            model: placeModelObj
        });
        placeViewObj.$el.insertBefore('#chapter-content-'+placeModelObj.get('chapter_id')+' .place-add-wrap');
    },
    //显示添加表单
    showChapterEdit: function(){
        var editViewObj = new ChapterEditViewClass({type: 'create'});
    },
    //移动位置
    move: function(srcIndex, destIndex){
        //总条数
        var total = chapterNav.children().length;
        if (destIndex < 0 || destIndex >= total) {
            return false;
        }

        var srcNavItem = chapterNav.children().eq(srcIndex);
        var srcContentItem = chapterContent.children().eq(srcIndex);
        var destNavItem = chapterNav.children().eq(destIndex);
        var destContentItem = chapterContent.children().eq(destIndex);

        if (destIndex < srcIndex) {//上移
            srcNavItem.insertBefore(destNavItem);
            srcContentItem.insertBefore(destContentItem);
        } else {//下移
            srcNavItem.insertAfter(destNavItem);
            srcContentItem.insertAfter(destContentItem);
        }
        //排序结果
        this.resetOrder();
    },
    //服务器端刷新排序
    resetOrder: function(){
        var orderStr = chapterNav.sortable('serialize');
        orderStr = orderStr.replace(/chapter-nav/g, 'order');
        var url = 'index.php?g=Rest&m=StrategyChapter&a=order';
        url += '&'+orderStr;
        $.get(url, function(data){
           //
        });
    }
});

//实例化章节视图
var chapterAppViewObj = new ChapterAppViewClass();
//排序
chapterNav.sortable({
    stop: function(){
        chapterAppViewObj.resetOrder();
    }
});