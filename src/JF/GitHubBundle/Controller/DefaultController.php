<?php

namespace JF\GitHubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/github")
 * @Template()
 */
class DefaultController extends Controller {

    use \Ephp\ACLBundle\Controller\Traits\NotifyController,
        \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * @Route("/webhook", defaults={"_format": "josn"})
     * @Method("POST")
     */
    public function indexAction() {
        $request = $this->getRequest();

        $user = $this->find('JFACLBundle:Gestore', 1);
        $json = json_decode(str_replace('payload=', '', urldecode($request->getContent())));

        $branch = $this->container->getParameter('github.branch');

        if ('refs/heads/' . $branch == $json->ref) {
            $files = array();
            foreach ($json->commits as $commit) {
                foreach ($commit->added as $file) {
                    $files[] = $file;
                }
                foreach ($commit->removed as $file) {
                    $files[] = $file;
                }
                foreach ($commit->modified as $file) {
                    $files[] = $file;
                }
            }

            $deploy = $this->container->getParameter('github.deploy');
            $name = $this->container->getParameter('github.name');
            $path = $this->container->getParameter('github.path');

            $pre = $deploy['sudo'] ? 'sudo ' : '';

            $sh = "
#!/bin/sh
echo \"Aggiornamento di {$name}\"
cd {$path}
sleep 1
";
            switch (strtolower($deploy['cache'])) {
                case 'always':
                    $sh .= "
echo \"Cancella cache\"
rm -rf {$path}/app/cache/*
# php app/console cache:clear
                ";
                    break;
            }
            $sh .= "
echo \"Allinea Git\"
git fetch
git checkout -f origin/{$branch}
                ";
            switch (strtolower($deploy['composer'])) {
                case 'always':
                    $sh .= "
echo \"Lancia Composer\"
php composer.phar update
                ";
                    break;
            }
            switch (strtolower($deploy['db'])) {
                case 'doctrine':
                    $sh .= "
echo \"Aggiorna DB\"
php app/console doctrine:schema:update --dump-sql
php app/console doctrine:schema:update --force
                ";
                    break;
            }
            switch (strtolower($deploy['install'])) {
                case 'always':
                    $sh .= "
echo \"Assets install\"
php app/console assets:install
                ";
                    break;
            }
            switch (strtolower($deploy['dump'])) {
                case 'always':
                    $sh .= "
echo \"Assetic dump\"
php app/console assetic:dump
                ";
                    break;
            }
            switch (strtolower($deploy['warmup'])) {
                case 'always':
                    $sh .= "
echo \"ricostruzione cache\"
php app/console cache:warmup --env=prod --no-debug
                ";
                    break;
            }
            if (isset($deploy['chown'])) {
                $sh .= "
echo \"CHOWN\"
chown -R {$deploy['chown']} {$path}
                ";
            }
            $sh .= "
echo \"Finito\"
                ";

            $file = $this->container->getParameter('github.script');
            ;

            $handle = fopen($file, 'w');
            fwrite($handle, $sh);
            fclose($handle);
            chmod($file, 0777);

            $output = exec($file, $exec);

            unlink($file);

            $out = array(
                'type' => \Ephp\UtilityBundle\Utility\Debug::typeof($exec),
                'content' => implode('<hr/>', array(
                    $sh,
                    implode('<br/>', $exec),
                    implode('<br/>', $files),
                ))
            );

            $this->notify($user, 'GitHub Notify', 'JFGitHubBundle:email:test', $out);
            return $this->jsonResponse($out);
        }
        return $this->jsonResponse();
    }

}
