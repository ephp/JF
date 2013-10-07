<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JF\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use JF\CoreBundle\Command\Helper\DialogHelper;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;

/**
 * Generates a CRUD for a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class InstallCommand extends DoctrineCommand {

    /**
     * @see Command
     */
    protected function configure() {
        $this
                ->setDefinition(array())
                ->setDescription('Installa e aggiorna i Package di JF-System')
                ->setHelp(<<<EOT
Il comando <info>jf:package:install</info> installa i package e le license a loro relative.

<info>php app/console jf:package:install</info>
EOT
                )
                ->setName('jf:package:install')
                ->setAliases(array('jf:package:install', 'jf:install'))
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $dialog = $this->getDialogHelper();
            $dialog->writeSection($output, 'Installazione paccketti e licenze JF-SYSTEM');

        $out = array();
        foreach ($this->getContainer()->getParameter('jf.install') as $controller => $action) {
            $dialog->writeSection($output, $controller . '::' . $action, 'bg=white;fg=black');
            $_controller = new $controller();
            $_controller->setContainer($this->getContainer());
            $temp = $_controller->$action();
            print_r(json_encode($temp));
            $out[] = $temp;
        }
        
        $dialog->writeSection($output, 'Installazione pacchetti e licenze JF-SYSTEM compleatata');
    }

    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEm() {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        return $em;
    }

    /**
     * 
     * @return \Ephp\PortletBundle\Command\Helper\DialogHelper
     */
    protected function getDialogHelper() {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Ephp\TagBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

}
