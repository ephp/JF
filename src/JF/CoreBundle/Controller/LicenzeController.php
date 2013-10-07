<?php

namespace JF\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JF\CoreBundle\Entity\Licenza;

/**
 * Licenza controller.
 *
 * @Route("/eph/licenze")
 */
class LicenzeController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * Lists all Licenza entities.
     *
     * @Route("/{cliente}", name="eph_licenze", options={"ACL": {"in_role": {"R_EPH"}}})
     * @Method("GET")
     * @Template()
     */
    public function indexAction($cliente) {
        $cliente = $this->find('JFACLBundle:Cliente', $cliente);
        $entities = array();
        
        $licenze = $this->findBy('JFCoreBundle:Licenza', array());
        foreach ($licenze as $licenza) {
            if (!isset($entities[$licenza->getGruppo()->getSiglaCompleta()])) {
                $entities[$licenza->getGruppo()->getSiglaCompleta()] = array('gruppo' => $licenza->getGruppo(), 'licenze' => array(), 'max' => 0);
            }
            $entities[$licenza->getGruppo()->getSiglaCompleta()]['licenze'][] = $licenza;
        }

        return array(
            'entities' => $entities,
            'ordine' => $this->getUltimoOrdine($cliente),
            'cliente' => $cliente,
        );
    }

    /**
     * Finds and displays a Licenza entity.
     *
     * @Route("/{package}-{gruppo}-{sigla}/{cliente}", name="eph_licenze_show", options={"ACL": {"in_role": {"R_EPH"}}})
     * @Method("GET")
     * @Template()
     */
    public function showAction($package, $gruppo, $sigla, $cliente) {
        $cliente = $this->find('JFACLBundle:Cliente', $cliente);
        $package = $this->findOneBy('JFCoreBundle:Package', array('sigla' => $package));
        $gruppo = $this->findOneBy('JFCoreBundle:Gruppo', array('package' => $package->getId(), 'sigla' => $gruppo));
        $entity = $this->findOneBy('JFCoreBundle:Licenza', array('gruppo' => $gruppo->getId(), 'sigla' => $sigla));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Licenza entity.');
        }

        return array(
            'entity' => $entity,
            'ordine' => $this->getUltimoOrdine($cliente),
            'cliente' => $cliente,
        );
    }

    /**
     * Finds and displays a Licenza entity.
     *
     * @Route("/{package}-{gruppo}-{sigla}/buy/{cliente}", name="eph_licenze_buy", options={"ACL": {"in_role": {"R_EPH"}}})
     * @Method("GET")
     * @Template()
     */
    public function buyLicenzaAction($package, $gruppo, $sigla, $cliente) {
        $cliente = $this->find('JFACLBundle:Cliente', $cliente);
        $package = $this->findOneBy('JFCoreBundle:Package', array('sigla' => $package));
        $gruppo = $this->findOneBy('JFCoreBundle:Gruppo', array('package' => $package->getId(), 'sigla' => $gruppo));
        $entity = $this->findOneBy('JFCoreBundle:Licenza', array('gruppo' => $gruppo->getId(), 'sigla' => $sigla));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Licenza entity.');
        }

        $ordine = $this->getUltimoOrdine($cliente);

        foreach ($ordine->getProdotti() as $prodotto) {
            /* @var $prodotto \JF\CoreBundle\Entity\Prodotto */
            if ($prodotto->getLicenza()->getGruppo() == $entity->getGruppo()) {
                $this->remove($prodotto);
            }
        }

        $prodotto = new \JF\CoreBundle\Entity\Prodotto();
        $prodotto->setLicenza($entity);
        $prodotto->setOrdine($ordine);
        $prodotto->setQuantita(1);
        $prodotto->setPrezzo($entity->getPrezzo());

        $this->persist($prodotto);

        return $this->redirect($this->generateUrl('eph_licenze', array('cliente' => $cliente->getId())));
    }

    /**
     * Finds and displays a Licenza entity.
     *
     * @Route("/buy/{cliente}", name="eph_licenze_carrello_buy", options={"ACL": {"in_role": {"R_EPH"}}})
     * @Method("GET")
     * @Template()
     */
    public function buyAction($cliente) {
        $cliente = $this->find('JFACLBundle:Cliente', $cliente);
        $ordine = $this->getUltimoOrdine($cliente);

        /* @var $cliente \JF\ACLBundle\Entity\Cliente */

        try {
            $this->getEm()->beginTransaction();
            foreach ($ordine->getProdotti() as $prodotto) {
                /* @var $prodotto \JF\CoreBundle\Entity\Prodotto */
                $_licenza = new \JF\ACLBundle\Entity\Licenza();
                while ($licenza = $cliente->getLicenzaGruppo($prodotto->getLicenza()->getGruppo())) {
                    if ($licenza) {
                        $_licenza->setLicenzaPrecedente($licenza->getLicenza());
                        $this->remove($licenza);
                    }
                }
                $_licenza->setLicenza($prodotto->getLicenza());
                $_licenza->setCliente($cliente);
                $_licenza->setPagamento(new \DateTime());
                $this->persist($_licenza);
            }
            $ordine->setOrdinato(new \DateTime());
            $this->persist($ordine);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }

        return $this->redirect($this->generateUrl('eph_clienti_show', array('id' => $cliente->getId())));
    }

    /**
     * 
     * @return \JF\CoreBundle\Entity\Ordine
     */
    private function getUltimoOrdine(\JF\ACLBundle\Entity\Cliente $cliente) {
        $ordine = $this->findOneBy('JFCoreBundle:Ordine', array('cliente' => $cliente->getId(), 'omaggio' => true, 'cancellazione' => null, 'ordinato' => null));

        if (!$ordine) {
            $ordine = new \JF\CoreBundle\Entity\Ordine();
            $ordine->setCliente($cliente);
            $ordine->setOmaggio(true);
            $this->persist($ordine);
        }

        return $ordine;
    }

}
