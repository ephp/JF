<?php

namespace JF\CoreBundle\DependencyInjection\Interfaces;

interface IExtension {

    public function setMenu(\Symfony\Component\DependencyInjection\ContainerBuilder $container);

    public function setWidgets(\Symfony\Component\DependencyInjection\ContainerBuilder $container);

    public function setPackage(\Symfony\Component\DependencyInjection\ContainerBuilder $container);

    public function setRoles(\Symfony\Component\DependencyInjection\ContainerBuilder $container);

    public function setInstall(\Symfony\Component\DependencyInjection\ContainerBuilder $container);
}
