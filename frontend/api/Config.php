<?php
return [
    //TODO: 轮播图接口
    'create_carousels' => 'BooksBackend\CreateCarousels',
    'get_carousels' => 'BooksBackend\GetCarousels',
    'delete_carousels' => 'BooksBackend\DelCarousels',
    'update_carousels' => 'BooksBackend\UpdateCarousels',
    'get_carousel' => 'BooksBackend\GetCarousel',
    //TODO: 周刊接口
    'create_weekly' => 'BooksBackend\CreateWeekly',
    'update_weekly' => 'BooksBackend\UpdateWeekly',
    'get_weekly' => 'BooksBackend\GetWeekly',
    'get_one_weekly' => 'BooksBackend\GetOneWeekly',
    //TODO: 书籍接口
    'create_book' => 'BooksBackend\CreateBook',
    'update_book' => 'BooksBackend\UpdateBook',
    'get_books' => 'BooksBackend\GetBooks',
    'get_book' => 'BooksBackend\GetBook',
    //TODO: 文章接口
    'create_article' => 'BooksBackend\CreateArticle',
    'update_article' => 'BooksBackend\UpdateArticle',
    'get_articles' => 'BooksBackend\GetArticles',
    'get_article' => 'BooksBackend\GetArticle',
    'create_article_params'=>'BooksBackend\CreateArticleParams',
    'update_article_params'=>'BooksBackend\UpdateArticleParams',
    'del_article_params' =>'BooksBackend\DeleteArticleParams',
    'get_article_params_list'=>'BooksBackend\GetArticleParamsList',
    'get_article_params' => 'BooksBackend\GetArticleParams',
    //TODO: Web接口
    'web_get_carousels'=>'BooksBackend\WebGetCarousels',
    'web_get_issue' => 'BooksBackend\WebGetIssue',
    'web_get_books' => 'BooksBackend\WebGetBooks',
    'web_get_articles' => 'BooksBackend\WebGetArticles',
    //TODO: 获取管理员登录信息接口
    'get_user_info' => 'GetUserInfo',
    'user_login' => 'WxUserLogin',

    //TODO: 微信小程序
    'add_user' => 'ReadingBook\AddUser',
    'add_dynamic' => 'ReadingBook\AddDynamic',
    'add_comment' => 'ReadingBook\AddComment',
    'get_dynamic' => 'ReadingBook\GetDynamic',
    'get_dynamic_list' => 'ReadingBook\GetDynamicList',
    'get_comment_list' => 'ReadingBook\GetCommentList',
    'add_collect' => 'ReadingBook\AddCollect',
    'get_collect_list' => 'ReadingBook\GetCollectList',
    'del_dynamic' => 'ReadingBook\DelDynamic',
    'get_book_carousels' => 'ReadingBook\GetCarousels',
    'del_collect' => 'ReadingBook\DelCollect',
    'get_user' => 'ReadingBook\GetUser',
];