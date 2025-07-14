<?php
return [
  '/' => [
    'GET' => [
      'controller' => 'HomeController',
      'action' => 'index',
      'middleware' => ['basic']
    ]
  ],
  '/dettaglio-prodotto/{id}' => [
    'GET' => [
      'controller' => 'ProductDetailController',
      'action' => 'index',
      'middleware' => ['basic']

    ]
  ],
  '/aggiungi-recensione' => [
    'GET' => [
      'controller' => 'AddReviewController',
      'action' => 'index',
      'middleware' => ['basic', 'auth', 'customer', 'review']
    ],
    'POST' => [
      'controller' => 'AddReviewController',
      'action' => 'addReview',
      'middleware' => ['basic', 'auth', 'customer']
    ]
  ],
  '/aggiungi-prodotto' => [
    'GET' => [
      'controller' => 'ProductHandleController',
      'action' => 'indexAdd',
      'middleware' => ['basic', 'auth', 'vendor']
    ],
    'POST' => [
      'controller' => 'ProductHandleController',
      'action' => 'addProduct',
      'middleware' => ['basic', 'auth', 'vendor']
    ]
  ],
  '/modifica-prodotto/{id}' => [
    'GET' => [
      'controller' => 'ProductHandleController',
      'action' => 'indexEdit',
      'middleware' => ['basic', 'auth', 'vendor']
    ],
    'POST' => [
      'controller' => 'ProductHandleController',
      'action' => 'editProduct',
      'middleware' => ['basic', 'auth', 'vendor']
    ]
  ],
  '/ricarica-prodotto/{id}' => [
    'POST' => [
      'controller' => 'ProductHandleController',
      'action' => 'reloadProduct',
      'middleware' => ['basic', 'auth', 'vendor']
    ]
  ],
  '/cancella-prodotto/{id}' => [
    'DELETE' => [
      'controller' => 'ProductHandleController',
      'action' => 'deleteProduct',
      'middleware' => ['basic', 'auth', 'vendor']
    ]
  ],
  '/carrello' => [
    'GET' => [
      'controller' => 'ShopCartController',
      'action' => 'index',
      'middleware' => ['basic', 'client']
    ]
  ],
  '/carrello/add-product' => [
    'POST' => [
      'controller' => 'ShopCartController',
      'action' => 'addProduct',
      'middleware' => ['basic', 'client']
    ]
  ],
  '/carrello/remove-product' => [
    'POST' => [
      'controller' => 'ShopCartController',
      'action' => 'removeProduct',
      'middleware' => ['basic', 'client']
    ]
  ],
  '/carrello/update-quantity' => [
    'POST' => [
      'controller' => 'ShopCartController',
      'action' => 'updateQuantity',
      'middleware' => ['basic', 'client']
    ]
  ],
  '/carrello/count-products' => [
    'GET' => [
      'controller' => 'ShopCartController',
      'action' => 'countProductsInCart',
      'middleware' => ['basic', 'client']
    ]
  ],
  // Checkout
  // Visitabile solamente da cliente
  '/checkout' => [
    'GET' => [
      'controller' => 'CheckoutController',
      'action' => 'index',
      'middleware' => ['basic', 'auth', 'client']
    ],
    'POST' => [
      'controller' => 'CheckoutController',
      'action' => 'index',
      'middleware' => ['basic', 'auth', 'client']
    ]
  ],
  '/checkout/payment/card' => [
    'POST' => [
      'controller' => 'CheckoutController',
      'action' => 'paymentCard',
      'middleware' => ['basic', 'auth', 'client']
    ]
  ],
  '/checkout/payment/delivery' => [
    'GET' => [
      'controller' => 'CheckoutController',
      'action' => 'paymentDelivery',
      'middleware' => ['basic', 'auth', 'client']
    ]
  ],
  // Dashboard
  // Visitabile solamente da venditore
  '/dashboard-venditore' => [
    'GET' => [
      'controller' => 'VendorDashboardController',
      'action' => 'index',
      'middleware' => ['basic', 'auth', 'vendor']
    ]
  ],
  // Login
  // Visitabile solamente da cliente e da utente non loggato
  '/login' => [
    'GET' => [
      'controller' => 'LoginController',
      'action' => 'indexClient'
    ],
    'POST' => [
      'controller' => 'LoginController',
      'action' => 'loginAsClient'
    ]
  ],
  '/login-vendor' => [
    'GET' => [
      'controller' => 'LoginController',
      'action' => 'indexVendor'
    ],
    'POST' => [
      'controller' => 'LoginController',
      'action' => 'loginAsVendor'
    ]
  ],
  '/signup' => [
    'GET' => [
      'controller' => 'SignupController',
      'action' => 'index'
    ],
    'POST' => [
      'controller' => 'SignupController',
      'action' => 'signup'
    ]
  ],
  '/logout' => [
    'GET' => [
      'controller' => 'LogoutController',
      'action' => 'index',
    ]
  ],
  '/storico-ordini' => [
    'GET' => [
      'controller' => 'OrderHistoryController',
      'action' => 'index',
      'middleware' => ['basic', 'auth']
    ]
  ],
  '/orders/update-status' => [
    'POST' => [
      'controller' => 'OrderHistoryController',
      'action' => 'updateStatus',
      'middleware' => ['basic', 'auth', 'vendor']
    ]
  ],
  '/dettaglio-ordine/{id}' => [
    'GET' => [
      'controller' => 'OrderDetailController',
      'action' => 'index',
      'middleware' => ['basic', 'auth', 'order']
    ],
    'DELETE' => [
      'controller' => 'OrderDetailController',
      'action' => 'cancelOrder',
      'middleware' => ['basic', 'auth', 'client']
    ],
    'POST' => [
      'controller' => 'OrderDetailController',
      'action' => 'markAsPaid',
      'middleware' => ['basic', 'auth', 'vendor']
    ]
  ],
  '/user-profile' => [
    'GET' => [
      'controller' => 'UserProfileController',
      'action' => 'index',
      'middleware' => ['basic', 'auth']
    ]
  ],
  '/user-profile/modifica' => [
    'POST' => [
      'controller' => 'UserProfileController',
      'action' => 'updateProfile',
      'middleware' => ['basic', 'auth']
    ]
  ],
  '/notifiche' => [
    'GET' => [
      'controller' => 'NotificationController',
      'action' => 'index',
      'middleware' => ['basic', 'auth']
    ]
  ],
  '/notifiche/fetch' => [
    'GET' => [
      'controller' => 'NotificationController',
      'action' => 'fetchNotifications',
      'middleware' => ['basic', 'auth']
    ]
  ],
  '/notifiche/mark-as-read/{id}' => [
    'POST' => [
      'controller' => 'NotificationController',
      'action' => 'markAsRead',
      'middleware' => ['basic', 'auth']
    ]
  ],
  '/notifiche/mark-all-as-read' => [
    'POST' => [
      'controller' => 'NotificationController',
      'action' => 'markAllAsRead',
      'middleware' => ['basic', 'auth']
    ]
  ],
  '/notifiche/delete/{id}' => [
    'DELETE' => [
      'controller' => 'NotificationController',
      'action' => 'deleteNotification',
      'middleware' => ['basic', 'auth']
    ]
  ],
  '/notifiche/unread-count' => [
    'GET' => [
      'controller' => 'NotificationController',
      'action' => 'getUnreadCount',
      'middleware' => ['basic', 'auth']
    ]
  ],
  // Errori
  '/404' => [
    'GET' => [
      'controller' => 'ErrorController',
      'action' => 'error404'
    ],
    'POST' => [
      'controller' => 'ErrorController',
      'action' => 'error404'
    ],
    'PUT' => [
      'controller' => 'ErrorController',
      'action' => 'error404'
    ],
    'DELETE' => [
      'controller' => 'ErrorController',
      'action' => 'error404'
    ]
  ],
  '/403' => [
    'GET' => [
      'controller' => 'ErrorController',
      'action' => 'error403'
    ],
    'POST' => [
      'controller' => 'ErrorController',
      'action' => 'error403'
    ],
    'PUT' => [
      'controller' => 'ErrorController',
      'action' => 'error403'
    ],
    'DELETE' => [
      'controller' => 'ErrorController',
      'action' => 'error403'
    ]
  ],
];
