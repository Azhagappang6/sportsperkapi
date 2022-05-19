<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'security.authentication.listener.form.oauth_authorize' shared service.

include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Firewall\\ListenerInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Firewall\\LegacyListenerTrait.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Firewall\\AbstractAuthenticationListener.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Firewall\\UsernamePasswordFormAuthenticationListener.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Authentication\\AuthenticationSuccessHandlerInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Util\\TargetPathTrait.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Authentication\\DefaultAuthenticationSuccessHandler.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Authentication\\AuthenticationFailureHandlerInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Authentication\\DefaultAuthenticationFailureHandler.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Session\\SessionAuthenticationStrategyInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\security-http\\Session\\SessionAuthenticationStrategy.php';

$a = ($this->privates['security.http_utils'] ?? $this->load('getSecurity_HttpUtilsService.php'));
$b = new \Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler($a, []);
$b->setOptions(['login_path' => '/oauth/v2/auth_login', 'use_referer' => true, 'always_use_default_target_path' => false, 'default_target_path' => '/', 'target_path_parameter' => '_target_path']);
$b->setProviderKey('oauth_authorize');
$c = ($this->privates['logger'] ?? ($this->privates['logger'] = new \Symfony\Component\HttpKernel\Log\Logger()));

$d = new \Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler(($this->services['http_kernel'] ?? $this->getHttpKernelService()), $a, [], $c);
$d->setOptions(['login_path' => '/oauth/v2/auth_login', 'failure_path' => NULL, 'failure_forward' => false, 'failure_path_parameter' => '_failure_path']);

return $this->privates['security.authentication.listener.form.oauth_authorize'] = new \Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener(($this->services['security.token_storage'] ?? ($this->services['security.token_storage'] = new \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage())), ($this->privates['security.authentication.manager'] ?? $this->getSecurity_Authentication_ManagerService()), ($this->privates['security.authentication.session_strategy'] ?? ($this->privates['security.authentication.session_strategy'] = new \Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy('migrate'))), $a, 'oauth_authorize', $b, $d, ['check_path' => '/oauth/v2/auth_login_check', 'use_forward' => false, 'require_previous_session' => false, 'username_parameter' => '_username', 'password_parameter' => '_password', 'csrf_parameter' => '_csrf_token', 'csrf_token_id' => 'authenticate', 'post_only' => true], $c, ($this->services['event_dispatcher'] ?? $this->getEventDispatcherService()), NULL);
