<?php
return [
    'index' => [
        'title' => '首页',
        'icon' => 'near_me',
        'iconColor' => 'blue',
        'page' => 'Index',
    ],
    'reading' => [
        'title' => '阅读',
        'icon' => 'description',
        'iconColor' => 'blue-grey',
        'items' => [
            'navigation' => [
                'title' => '导航',
                'page' => 'Navigation',
            ],
            'searching' => [
                'title' => '搜索',
                'page' => 'Searching',
            ],
            'tracking-changes' => [
                'title' => '跟踪更改',
                'page' => 'Tracking_changes',
            ],
            'watchlist' => [
                'title' => '监视列表/收藏列表',
                'page' => 'Watchlist',
            ]
        ],
    ],
    'editing' => [
        'title' => '编辑',
        'icon' => 'edit',
        'iconColor' => 'deep-orange',
        'items' => [
            'editing-pages' => [
                'title' => '编辑页面',
                'page' => 'Editing_pages',
            ],
            'creating-pages' => [
                'title' => '创建新页面',
                'page' => 'Starting_a_new_page',
            ],
            'formatting' => [
                'title' => '格式化文本',
                'page' => 'Formatting',
            ],
            'links' => [
                'title' => '链接',
                'page' => 'Links',
            ],
            'user-page' => [
                'title' => '用户页',
                'page' => 'User_page',
            ],
            'talk-pages' => [
                'title' => '讨论页面',
                'page' => 'Talk_pages',
            ],
            'signatures' => [
                'title' => '签名',
                'page' => 'Signatures',
            ],
            've' => [
                'title' => '可视化编辑器',
                'page' => 'VisualEditor/User_guide',
            ],
        ],
    ],
    'advanced-editing' => [
        'title' => '进阶编辑',
        'icon' => 'web',
        'iconColor' => 'brown',
        'items' => [
            'images' => [
                'title' => '图片',
                'page' => 'Images',
            ],
            'lists' => [
                'title' => '列表',
                'page' => 'Lists',
            ],
            'tables' => [
                'title' => '表格',
                'page' => 'Tables',
            ],
            'categories' => [
                'title' => '分类',
                'page' => 'Categories',
            ],
            'subpages' => [
                'title' => '子页面',
                'page' => 'Subpages',
            ],
            'managing-files' => [
                'title' => '文件管理',
                'page' => 'Managing_files',
            ],
            'move-page' => [
                'title' => '移动（重命名）页面',
                'page' => 'Moving_a_page',
            ],
            'redirects' => [
                'title' => '重定向',
                'page' => 'Redirects',
            ],
            'protected-pages' => [
                'title' => '保护页面',
                'page' => 'Protected_pages',
            ],
            'templates' => [
                'title' => '模板',
                'page' => 'Templates',
            ],
            'magic-words' => [
                'title' => '魔术字',
                'page' => 'Magic_words',
            ],
            'namespaces' => [
                'title' => '名字空间',
                'page' => 'Namespaces',
            ],
            'references' => [
                'title' => '引用',
                'page' => 'Cite',
            ],
            'special-pages' => [
                'title' => '特殊页面',
                'page' => 'Special_pages',
            ],
            'external-searches' => [
                'title' => '外部搜索',
                'page' => 'External_searches',
            ],
            'bots' => [
                'title' => '机器人',
                'page' => 'Bots',
            ]
        ],
    ],
    'collaboration' => [
        'title' => '合作',
        'icon' => 'question_answer',
        'iconColor' => 'teal',
        'items' => [
            'notifications' => [
                'title' => '通知',
                'page' => 'Notifications'
            ],
            'discussions' => [
                'title' => '结构化讨论',
                'page' => 'Structured_Discussions',
            ],
        ],
    ],
    'personal-custom' => [
        'title' => '个性化',
        'icon' => 'settings',
        'iconColor' => 'indigo',
        'items' => [
            'preferences' => [
                'title' => '参数设置',
                'page' => 'Preferences',
            ],
            'skins' => [
                'title' => '皮肤',
                'page' => 'Skins',
            ],
        ],
    ],
    'wiki-admin' => [
        'title' => '管理',
        'icon' => 'dashboard',
        'iconColor' => 'red',
        'items' => [
            'sysop' => [
                'title' => '管理员和权限',
                'page' => 'Sysops_and_permissions',
            ],
            'protect' => [
                'title' => '保护页面和移除页面保护',
                'page' => 'Protecting_and_unprotecting pages',
            ],
            'delete' => [
                'title' => '删除和撤销删除',
                'page' => 'Deletion_and_undeletion',
            ],
            'patrol' => [
                'title' => '巡查编辑',
                'page' => 'Patrolled_edits',
            ],
            'block' => [
                'title' => '封禁用户',
                'page' => 'Blocking_users',
            ],
            'block-ip' => [
                'title' => '封禁IP段',
                'page' => 'Range_blocks',
            ],
            'permission' => [
                'title' => '用户权限与用户组',
                'page' => 'User_rights_and_groups',
            ],
        ],
    ],
];