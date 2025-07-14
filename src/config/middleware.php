<?php
return [
  'basic' => ['ClearRedirectMiddleware', 'SessionValidatorMiddleware'],
  'auth' => ['AuthMiddleware'],
  'client' => ['CheckClientMiddleware'],
  'vendor' => ['CheckVendorMiddleware'],
  'review' => ['CheckReviewMiddleware'],
  'order' => ['CheckOwnOrdersMiddleware'],
];