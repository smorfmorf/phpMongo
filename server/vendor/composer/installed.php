<?php return array(
    'root' => array(
        'name' => 'm.p.yuzik/server',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => null,
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'm.p.yuzik/server' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => null,
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'mongodb/builder' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'mongodb/mongodb' => array(
            'pretty_version' => '1.21.2',
            'version' => '1.21.2.0',
            'reference' => '0e80d179370b9d6338df4e62c292c3652e0a2396',
            'type' => 'library',
            'install_path' => __DIR__ . '/../mongodb/mongodb',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'psr/log' => array(
            'pretty_version' => '3.0.2',
            'version' => '3.0.2.0',
            'reference' => 'f16e1d5863e37f8d8c2a01719f5b34baa2b714d3',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/log',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
