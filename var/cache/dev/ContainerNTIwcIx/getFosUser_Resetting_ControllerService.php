<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'fos_user.resetting.controller' shared service.

include_once $this->targetDirs[3].'\\vendor\\symfony\\dependency-injection\\ContainerAwareInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\dependency-injection\\ContainerAwareTrait.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\framework-bundle\\Controller\\ControllerTrait.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\framework-bundle\\Controller\\Controller.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\user-bundle\\Controller\\ResettingController.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\user-bundle\\Form\\Factory\\FactoryInterface.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\user-bundle\\Form\\Factory\\FormFactory.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\user-bundle\\Util\\TokenGeneratorInterface.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\user-bundle\\Util\\TokenGenerator.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\user-bundle\\Mailer\\MailerInterface.php';
include_once $this->targetDirs[3].'\\vendor\\friendsofsymfony\\user-bundle\\Mailer\\Mailer.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\mailer\\MailerInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\mailer\\Mailer.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\mailer\\Transport.php';

$a = ($this->services['event_dispatcher'] ?? $this->getEventDispatcherService());

$this->services['fos_user.resetting.controller'] = $instance = new \FOS\UserBundle\Controller\ResettingController($a, new \FOS\UserBundle\Form\Factory\FormFactory(($this->services['form.factory'] ?? $this->load('getForm_FactoryService.php')), 'fos_user_resetting_form', 'FOS\\UserBundle\\Form\\Type\\ResettingFormType', $this->parameters['fos_user.resetting.form.validation_groups']), ($this->services['fos_user.user_manager'] ?? $this->load('getFosUser_UserManagerService.php')), new \FOS\UserBundle\Util\TokenGenerator(), new \FOS\UserBundle\Mailer\Mailer(new \Symfony\Component\Mailer\Mailer(\Symfony\Component\Mailer\Transport::fromDsn($this->getEnv('MAILER_DSN'), $a, NULL, ($this->privates['logger'] ?? ($this->privates['logger'] = new \Symfony\Component\HttpKernel\Log\Logger()))), NULL), ($this->services['router'] ?? $this->getRouterService()), ($this->services['templating'] ?? $this->load('getTemplatingService.php')), ['confirmation.template' => '@FOSUser/Registration/email.txt.twig', 'resetting.template' => '@FOSUser/Resetting/email.txt.twig', 'from_email' => ['confirmation' => $this->parameters['fos_user.registration.confirmation.from_email'], 'resetting' => $this->parameters['fos_user.resetting.email.from_email']]]), 7200);

$instance->setContainer($this);

return $instance;
