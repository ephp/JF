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
 * @Route("/catalogo")
 */
class CatalogoController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * Lists all Licenza entities.
     *
     * @Route("/", name="catalogo", options={"ACL": {"in_role": {"R_SUPER"}}})
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $entities = array();
        foreach($this->getUser()->getCliente()->getLicenze() as $_licenza) {
            $licenza = $_licenza->getLicenza();
            if (!isset($entities[$licenza->getGruppo()->getSiglaCompleta()])) {
                $entities[$licenza->getGruppo()->getSiglaCompleta()] = array('gruppo' => $licenza->getGruppo(), 'licenze' => array(), 'max' => $licenza->getOrdine());
            }
            $entities[$licenza->getGruppo()->getSiglaCompleta()]['licenze'][] = $licenza;
        }
        
        $licenze = $this->findBy('JFCoreBundle:Licenza', array('market' => true));
        foreach ($licenze as $licenza) {
            if (!isset($entities[$licenza->getGruppo()->getSiglaCompleta()])) {
                $entities[$licenza->getGruppo()->getSiglaCompleta()] = array('gruppo' => $licenza->getGruppo(), 'licenze' => array(), 'max' => 0);
            }
            if($licenza->getOrdine() > $entities[$licenza->getGruppo()->getSiglaCompleta()]['max']) {
                $entities[$licenza->getGruppo()->getSiglaCompleta()]['licenze'][] = $licenza;
            }
        }

        return array(
            'entities' => $entities,
            'ordine' => $this->getUltimoOrdine(),
        );
    }

    /**
     * Finds and displays a Licenza entity.
     *
     * @Route("/{package}-{gruppo}-{sigla}", name="catalogo_show", options={"ACL": {"in_role": {"R_SUPER"}}})
     * @Method("GET")
     * @Template()
     */
    public function showAction($package, $gruppo, $sigla) {
        $package = $this->findOneBy('JFCoreBundle:Package', array('sigla' => $package));
        $gruppo = $this->findOneBy('JFCoreBundle:Gruppo', array('package' => $package->getId(), 'sigla' => $gruppo));
        $entity = $this->findOneBy('JFCoreBundle:Licenza', array('gruppo' => $gruppo->getId(), 'sigla' => $sigla));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Licenza entity.');
        }

        return array(
            'entity' => $entity,
            'ordine' => $this->getUltimoOrdine(),
        );
    }

    /**
     * Finds and displays a Licenza entity.
     *
     * @Route("/{package}-{gruppo}-{sigla}/buy", name="catalogo_buy", options={"ACL": {"in_role": {"R_SUPER"}}})
     * @Method("GET")
     * @Template()
     */
    public function buyLicenzaAction($package, $gruppo, $sigla) {
        $package = $this->findOneBy('JFCoreBundle:Package', array('sigla' => $package));
        $gruppo = $this->findOneBy('JFCoreBundle:Gruppo', array('package' => $package->getId(), 'sigla' => $gruppo));
        $entity = $this->findOneBy('JFCoreBundle:Licenza', array('gruppo' => $gruppo->getId(), 'sigla' => $sigla));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Licenza entity.');
        }

        $ordine = $this->getUltimoOrdine();

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

        return $this->redirect($this->generateUrl('catalogo'));
    }

    /**
     * Finds and displays a Licenza entity.
     *
     * @Route("/buy", name="catalogo_carrello_buy", options={"ACL": {"in_role": {"R_SUPER"}}})
     * @Method("GET")
     * @Template()
     */
    public function buyAction() {
        $ordine = $this->getUltimoOrdine();

        //TODO Dividere il processo in: pagamento, attivazione e fatturazione
        $cliente = $this->getUser()->getCliente();
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
        }

        return $this->redirect($this->generateUrl('index'));
    }

    /**
     * 
     * @return \JF\CoreBundle\Entity\Ordine
     */
    private function getUltimoOrdine() {
        $ordine = $this->findOneBy('JFCoreBundle:Ordine', array('cliente' => $this->getUser()->getCliente()->getId(), 'omaggio' => false, 'cancellazione' => null, 'ordinato' => null));

        if (!$ordine) {
            $ordine = new \JF\CoreBundle\Entity\Ordine();
            $ordine->setCliente($this->getUser()->getCliente());
            $ordine->setOmaggio(false);
            $this->persist($ordine);
        }

        return $ordine;
    }

}
