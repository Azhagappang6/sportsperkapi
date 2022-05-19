<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'routing.loader' shared service.

include_once $this->targetDirs[3].'\\vendor\\symfony\\config\\Loader\\LoaderInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\config\\Loader\\Loader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\config\\Loader\\DelegatingLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\framework-bundle\\Routing\\DelegatingLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\config\\Loader\\LoaderResolverInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\config\\Loader\\LoaderResolver.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\config\\Loader\\FileLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\XmlFileLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\YamlFileLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\PhpFileLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\GlobFileLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\DirectoryLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\ObjectRouteLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\DependencyInjection\\ServiceRouterLoader.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\rest-bundle\\Routing\\Loader\\DirectoryRouteLoader.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\rest-bundle\\Routing\\Loader\\RestRouteProcessor.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\rest-bundle\\Routing\\Loader\\RestRouteLoader.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\rest-bundle\\Routing\\Loader\\Reader\\RestControllerReader.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\rest-bundle\\Routing\\Loader\\Reader\\RestActionReader.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\rest-bundle\\Inflector\\InflectorInterface.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\rest-bundle\\Inflector\\DoctrineInflector.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\rest-bundle\\Routing\\Loader\\RestYamlCollectionLoader.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\rest-bundle\\Routing\\Loader\\RestXmlCollectionLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\AnnotationClassLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\framework-bundle\\Routing\\AnnotatedRouteControllerLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\AnnotationFileLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\routing\\Loader\\AnnotationDirectoryLoader.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\config\\FileLocatorInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\config\\FileLocator.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\http-kernel\\Config\\FileLocator.php';

$a = ($this->privates['controller_name_converter'] ?? ($this->privates['controller_name_converter'] = new \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser(($this->services['kernel'] ?? $this->get('kernel', 1)))));
$b = new \Symfony\Component\Config\Loader\LoaderResolver();

$c = ($this->privates['file_locator'] ?? ($this->privates['file_locator'] = new \Symfony\Component\HttpKernel\Config\FileLocator(($this->services['kernel'] ?? $this->get('kernel', 1)), ($this->targetDirs[3].'\\src/Resources'), [0 => ($this->targetDirs[3].'\\src')])));
$d = new \FOS\RestBundle\Routing\Loader\RestRouteProcessor();
$e = ($this->privates['annotations.cached_reader'] ?? $this->getAnnotations_CachedReaderService());
$f = new \Symfony\Bundle\FrameworkBundle\Routing\AnnotatedRouteControllerLoader($e);

$b->addLoader(new \Symfony\Component\Routing\Loader\XmlFileLoader($c));
$b->addLoader(new \Symfony\Component\Routing\Loader\YamlFileLoader($c));
$b->addLoader(new \Symfony\Component\Routing\Loader\PhpFileLoader($c));
$b->addLoader(new \Symfony\Component\Routing\Loader\GlobFileLoader($c));
$b->addLoader(new \Symfony\Component\Routing\Loader\DirectoryLoader($c));
$b->addLoader(new \Symfony\Component\Routing\Loader\DependencyInjection\ServiceRouterLoader($this));
$b->addLoader(new \FOS\RestBundle\Routing\Loader\DirectoryRouteLoader($c, $d));
$b->addLoader(new \FOS\RestBundle\Routing\Loader\RestRouteLoader($this, $c, $a, new \FOS\RestBundle\Routing\Loader\Reader\RestControllerReader(new \FOS\RestBundle\Routing\Loader\Reader\RestActionReader($e, ($this->privates['fos_rest.request.param_fetcher.reader'] ?? $this->getFosRest_Request_ParamFetcher_ReaderService()), new \FOS\RestBundle\Inflector\DoctrineInflector(), false, ['json' => false, 'html' => true], true), $e), 'json'));
$b->addLoader(new \FOS\RestBundle\Routing\Loader\RestYamlCollectionLoader($c, $d, false, ['json' => false, 'html' => true], 'json'));
$b->addLoader(new \FOS\RestBundle\Routing\Loader\RestXmlCollectionLoader($c, $d, false, ['json' => false, 'html' => true], 'json'));
$b->addLoader($f);
$b->addLoader(new \Symfony\Component\Routing\Loader\AnnotationDirectoryLoader($c, $f));
$b->addLoader(new \Symfony\Component\Routing\Loader\AnnotationFileLoader($c, $f));

return $this->services['routing.loader'] = new \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader($a, $b, ['utf8' => true]);
