<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    // Images & thumbs
    'maxImageWidth'                 => 800,
    'maxImageHeight'                => 800,
    'thumbnailDefaultWidth'         => 100,
    'thumbnailDefaultHeight'        => 100,
    'maxImageSize'                  => 1024 * 1024 * 5, // in bytes

    // URLS
    'frontendAbsoluteURL'           => 'http://testbook-frontend.loc',

    // frontend app directory
    'frontendDir'                   => Yii::getAlias(dirname(__FILE__) . '/../../frontend'),
    // frontend app directory
    'frontendWebrootDir'            => Yii::getAlias(dirname(__FILE__) . '/../../frontend/web'),
    // frontend uploads main directory
    'frontendUploadsDir'            => Yii::getAlias(dirname(__FILE__) . '/../../frontend/web/uploads'),
    // frontend uploads temporary directory
    'frontendUploadsTmpDir'         => Yii::getAlias(dirname(__FILE__) . '/../../frontend/web/uploads/tmp'),
    // books uploads dir
    'booksUploadsDir'               => Yii::getAlias(dirname(__FILE__) . '/../../frontend/web/uploads/books'),
];
