<?php
add_theme_support('post-thumbnails');

function registrar_cpt_json_bloco() {
    register_post_type('json_bloco', [
        'labels' => [
            'name' => 'Blocos JSON',
            'singular_name' => 'Bloco JSON'
        ],
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'custom-fields'],
        'menu_position' => 5,
        'menu_icon' => 'dashicons-admin-generic'
    ]);
}
add_action('init', 'registrar_cpt_json_bloco');

function registrar_api_json_bloco() {
    register_rest_route('json_bloco/v1', '/conteudo', [
        'methods' => 'GET',
        'callback' => 'obter_dados_json',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('json_bloco/v1', '/users', [
        'methods' => 'POST',
        'callback' => 'salvar_usuario',
        'permission_callback' => '__return_true',
        'args' => [
            'nome' => [
                'required' => true,
                'validate_callback' => function($param) {
                    return !empty($param);
                }
            ],
            'email' => [
                'required' => true,
                'validate_callback' => function($param) {
                    return filter_var($param, FILTER_VALIDATE_EMAIL);
                }
            ],
            'idade' => [
                'required' => false,
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param >= 0;
                }
            ],
            'cidade' => [
                'required' => false,
                'validate_callback' => function($param) {
                    return is_string($param);
                }
            ]
        ]
    ]);

    register_rest_route('json_bloco/v1', '/users', [
        'methods' => 'GET',
        'callback' => 'obter_usuarios',
        'permission_callback' => '__return_true',
        'args' => [
            'page' => [
                'default' => 1,
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                }
            ],
            'per_page' => [
                'default' => 10,
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0 && $param <= 100;
                }
            ]
        ]
    ]);
}
add_action('rest_api_init', 'registrar_api_json_bloco');

function processCards($cardsData) {
    $cards = [];
    if (!empty($cardsData)) {
        foreach ($cardsData as $cardKey => $card) {
            if (!empty($card['imagemUrl']) || !empty($card['title']) || !empty($card['description'])) {
                $cards[] = [
                    'id' => sanitize_key($cardKey),
                    'imagemUrl' => esc_url_raw($card['imagemUrl'] ?? ''),
                    'title' => sanitize_text_field($card['title'] ?? ''),
                    'description' => sanitize_text_field($card['description'] ?? '')
                ];
            }
        }
    }
    return $cards;
}

function processImages($imagesData) {
    $images = [];
    if (!empty($imagesData)) {
        foreach ($imagesData as $imageKey => $imageUrl) {
            if (!empty($imageUrl)) {
                $images[] = [
                    'id' => sanitize_key($imageKey),
                    'url' => esc_url_raw($imageUrl)
                ];
            }
        }
    }
    return $images;
}

function processTextCards($textCardsData) {
    $cards = [];
    if (!empty($textCardsData)) {
        foreach ($textCardsData as $cardKey => $card) {
            if (!empty($card['title']) || !empty($card['description'])) {
                $cards[] = [
                    'id' => sanitize_key($cardKey),
                    'title' => sanitize_text_field($card['title'] ?? ''),
                    'description' => sanitize_text_field($card['description'] ?? '')
                ];
            }
        }
    }
    return $cards;
}

function processAlbumImages($albumData) {
    $images = [];
    if (!empty($albumData['imagens'])) {
        if (isset($albumData['imagens'][0])) {
            foreach ($albumData['imagens'] as $index => $image) {
                if (!empty($image['imagemUrl'])) {
                    $images[] = [
                        'id' => $index,
                        'url' => esc_url_raw($image['imagemUrl'])
                    ];
                }
            }
        } else {
            foreach ($albumData['imagens'] as $key => $value) {
                if (strpos($key, 'imagemUrl') !== false && !empty($value)) {
                    $images[] = [
                        'id' => sanitize_key($key),
                        'url' => esc_url_raw($value)
                    ];
                }
            }
        }
    }
    return $images;
}

function processMenuCategories($categoriesData) {
    $categories = [];
    if (!empty($categoriesData)) {
        foreach ($categoriesData as $key => $value) {
            if (strpos($key, 'Categoria_') === 0 && !empty($value)) {
                $categories[] = [
                    'id' => sanitize_key($key),
                    'name' => sanitize_text_field($value)
                ];
            }
        }
    }
    return $categories;
}

function processar_usuarios($usuarios) {
    $resultado = [];
    if (!empty($usuarios)) {
        foreach ($usuarios as $index => $usuario) {
            if (!empty($usuario['user_data'])) {
                $resultado[] = [
                    'id' => $index,
                    'nome' => sanitize_text_field($usuario['user_data']['nome'] ?? ''),
                    'email' => sanitize_email($usuario['user_data']['email'] ?? ''),
                    'idade' => intval($usuario['user_data']['idade'] ?? null),
                    'cidade' => sanitize_text_field($usuario['user_data']['cidade'] ?? ''),
                    'data_cadastro' => sanitize_text_field($usuario['user_data']['data_cadastro'] ?? ''),
                ];
            }
        }
    }
    return $resultado;
}

function obter_dados_json() {
    $cache_key = 'json_bloco_content_data';
    $cached_data = get_transient($cache_key);

    if ($cached_data !== false) {
        return $cached_data;
    }

    $post = get_page_by_path('content', OBJECT, 'json_bloco');
    if (!$post) {
        return new WP_Error('not_found', 'Post "content" não encontrado', ['status' => 404]);
    }

    $campos = get_fields($post->ID);

    $formFields = [];
    if (!empty($campos['fields']['fields'])) {
        foreach ($campos['fields']['fields'] as $fieldKey => $fieldGroup) {
            if (is_array($fieldGroup) && isset($fieldGroup['name'], $fieldGroup['placeholder'], $fieldGroup['type'])) {
                $formFields[] = [
                    'id' => sanitize_key($fieldKey),
                    'name' => sanitize_text_field($fieldGroup['name']),
                    'placeholder' => sanitize_text_field($fieldGroup['placeholder']),
                    'type' => sanitize_text_field($fieldGroup['type']),
                    'required' => !empty($fieldGroup['required'])
                ];
            }
        }
    }

    $menuCategories = processMenuCategories($campos['menu']['category'] ?? []);

    $tags = [];
    if (!empty($campos['tags']['tagNames'])) {
        foreach ($campos['tags']['tagNames'] as $tagKey => $tagGroup) {
            if (!empty($tagGroup['tagName'])) {
                $tags[] = [
                    'id' => sanitize_key($tagKey),
                    'name' => sanitize_text_field($tagGroup['tagName'])
                ];
            }
        }
    }

    $footerLinks = [];
    if (!empty($campos['links_footer']['links'])) {
        foreach ($campos['links_footer']['links'] as $linkKey => $linkGroup) {
            if (!empty($linkGroup['name'])) {
                $footerLinks[] = [
                    'id' => sanitize_key($linkKey),
                    'name' => sanitize_text_field($linkGroup['name'])
                ];
            }
        }
    }

    $response = [
        "header" => [
            "title" => sanitize_text_field($campos['header']['title'] ?? ''),
            "subtitle" => sanitize_textarea_field($campos['header']['subtitle'] ?? ''),
            "buttonScroll" => esc_url_raw($campos['header']['buttonScroll'] ?? ''),
            "pattern" => esc_url_raw($campos['header']['pattern'] ?? '')
        ],
        "menu" => [
            "logo" => esc_url_raw($campos['menu']['logo'] ?? ''),
            "buttonBack" => esc_url_raw($campos['menu']['buttonBack'] ?? ''),
            "categories" => $menuCategories
        ],
        "image-cards" => [
            "title" => sanitize_text_field($campos['image_cards']['title'] ?? ''),
            "description" => sanitize_textarea_field($campos['image_cards']['description'] ?? ''),
            "cards" => processCards($campos['image_cards']['cards'] ?? [])
        ],
        "album" => [
            "title" => sanitize_text_field($campos['album']['title'] ?? ''),
            "description" => sanitize_textarea_field($campos['album']['description'] ?? ''),
            "images" => processAlbumImages($campos['album'] ?? [])
        ],
        "promo-app" => [
            "title" => sanitize_text_field($campos['promo_app']['title'] ?? ''),
            "description" => sanitize_textarea_field($campos['promo_app']['description'] ?? '')
        ],
        "tags" => [
            "title" => sanitize_text_field($campos['tags']['title'] ?? ''),
            "items" => $tags
        ],
        "text-cards" => [
            "items" => processTextCards($campos['text_cards']['text_cards'] ?? [])
        ],
        "form" => [
            "title" => sanitize_text_field($campos['form']['title'] ?? ''),
            "subtitle" => sanitize_text_field($campos['form']['subtitle'] ?? ''),
            "info" => sanitize_text_field($campos['form']['info'] ?? ''),
            "image" => esc_url_raw($campos['form']['image'] ?? '')
        ],
        "fields" => $formFields,
        "footer" => [
            "title" => sanitize_text_field($campos['links_footer']['title'] ?? ''),
            "links" => $footerLinks
        ],
        "users" => processar_usuarios($campos['users'] ?? [])
    ];

    set_transient($cache_key, $response, HOUR_IN_SECONDS);
    return $response;
}

function obter_usuarios($request) {
    $params = $request->get_params();
    $page = absint($params['page']);
    $per_page = absint($params['per_page']);

    $post = get_page_by_path('content', OBJECT, 'json_bloco');
    if (!$post) {
        return new WP_Error('not_found', 'Post "content" não encontrado', ['status' => 404]);
    }

    $usuarios = get_field('users', $post->ID) ?: [];
    $usuarios_processados = processar_usuarios($usuarios);

    $total_usuarios = count($usuarios_processados);
    $total_pages = ceil($total_usuarios / $per_page);

    $offset = ($page - 1) * $per_page;
    $usuarios_paginados = array_slice($usuarios_processados, $offset, $per_page);

    return [
        'data' => $usuarios_paginados,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $per_page,
            'total_items' => $total_usuarios,
            'total_pages' => $total_pages
        ]
    ];
}

function salvar_usuario($request) {
    $post = get_page_by_path('content', OBJECT, 'json_bloco');
    if (!$post) {
        return new WP_Error('not_found', 'Post "content" não encontrado', ['status' => 404]);
    }

    $data = $request->get_json_params();

    $novo_usuario = [
        'user_data' => [
            'nome' => isset($data['nome']) ? sanitize_text_field($data['nome']) : '',
            'email' => isset($data['email']) ? sanitize_email($data['email']) : '',
            'idade' => isset($data['idade']) ? intval($data['idade']) : null,
            'cidade' => isset($data['cidade']) ? sanitize_text_field($data['cidade']) : '',
            'data_cadastro' => current_time('mysql'),
        ]
    ];

    $usuarios = get_field('users', $post->ID) ?: [];
    $usuarios[] = $novo_usuario;
    
    update_field('users', $usuarios, $post->ID);

    delete_transient('json_bloco_content_data');

    return [
        'success' => true,
        'message' => 'Usuário cadastrado com sucesso',
        'user' => $novo_usuario['user_data']
    ];
}

function permitir_upload_svg($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'permitir_upload_svg');

function add_api_cache_headers($served, $result, $request, $server) {
    if (strpos($request->get_route(), '/json_bloco/v1/') === 0) {
        $server->send_header('Cache-Control', 'max-age=3600, must-revalidate');
    }
    return $served;
}
add_filter('rest_pre_serve_request', 'add_api_cache_headers', 10, 4);