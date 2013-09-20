<?php

namespace JF\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Menu\MenuItem;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Ephp\UtilityBundle\Utility\Debug;

class MenuBuilder {

    /**
     * @var FactoryInterface 
     */
    private $factory;

    /**
     * @var Request 
     */
    private $request;

    /**
     * @var SecurityContextInterface 
     */
    private $sc;

    /**
     * @var \Ephp\ACLBundle\Model\BaseUser 
     */
    private $user;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, SecurityContextInterface $sc) {
        $this->factory = $factory;
        $this->sc = $sc;
        if ($this->sc) {
            if (null !== $token = $this->sc->getToken()) {
                if (is_object($user = $token->getUser())) {
                    $this->user = $user;
                }
            }
        }
    }

    public function createSidebarMenu(Request $request, $voci) {
        $this->request = $request->getUser();
        $menu = $this->factory->createItem('root');
        $menu->setAttribute('id', 'menu_sidebar')->setAttribute('class', 'navigation bordered');

        $this->buildMenu($menu, $voci, $request->get('_route'));

        return $menu;
    }

    private function buildMenu(MenuItem $menu, $voci, $route) {
        usort($voci, function($a, $b) {
                    if (!isset($a['order'])) {
                        $a['order'] = 1000;
                    }
                    if (!isset($b['order'])) {
                        $b['order'] = 1000;
                    }
                    return $a['order'] > $b['order'];
                });
        foreach ($voci as $voce) {
            if (!isset($voce['show'])) {
                $voce['show'] = array('always' => true);
            }
            if ($this->show($voce['show'])) {
                if (isset($voce['route'])) {
                    $vm = $menu->addChild($voce['label'], array('route' => $voce['route']));
                    if($route == $voce['route']) {
                        $this->active($vm);
                    }
                } else {
                    if (isset($voce['submenu'])) {
                        $vm = $menu->addChild($voce['label'], array('url' => 'javascript:void(0);'));
                        $vm->setAttribute('class', 'submenu');
                        $this->buildMenu($vm, $voce['submenu'], $route);
                        if(!$vm->hasChildren()) {
                            $vm->setDisplay(false);
                        }
                    } else {
                        $vm = $menu->addChild($voce['label']);
                    }
                }
                if (isset($voce['a'])) {
                    foreach ($voce['a'] as $attr => $val) {
                        $vm->setLinkAttribute($attr, $val);
                    }
                }
            }
        }
    }

    private function active(\Knp\Menu\MenuItem $vm) {
        if(!$vm->isRoot()) {
            $vm->setAttribute('class', 'active');
            $this->active($vm->getParent());
        }
    }

    private function show($rules) {
        $out = false;
        if (isset($rules['always'])) {
            $out = $rules['always'];
        }
        if (isset($rules['logged'])) {
            $out = $rules['logged'] ? is_object($this->user) : !is_object($this->user);
        }
        if (is_object($this->user) && isset($rules['in_role'])) {
            if (!is_array($rules['in_role'])) {
                $rules['in_role'] = array($rules['in_role']);
            }
            foreach ($rules['in_role'] as $role) {
                $out |= $this->user->hasRole($role);
            }
        }
        if (is_object($this->user)) {
            if (isset($rules['out_role'])) {
                if (!is_array($rules['out_role'])) {
                    $rules['out_role'] = array($rules['out_role']);
                }
                $test = true;
                foreach ($rules['out_role'] as $role) {
                    $test &=!$this->user->hasRole($role);
                }
                $out = $test;
            }
            if ($this->user->getCliente() && isset($rules['license'])) {
                $active = $this->user->getCliente()->getLicenzeAttive();
                foreach ($rules['license'] as $gruppo => $licenses) {
                    $out &= in_array($active[$gruppo], $licenses);
                }
            }
        }

        return $out;
    }

}