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
     * @Route("/", name="catalogo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $licenze = $this->findBy('JFCoreBundle:Licenza', array('market' => true), array('gruppo' => 'ASC'));

        $entities = array();
        foreach ($licenze as $licenza) {
            if (!isset($entities[$licenza->getGruppo()])) {
                $entities[$licenza->getGruppo()] = array();
            }
            $entities[$licenza->getGruppo()][] = $licenza;
        }

        return array(
            'entities' => $entities,
            'ordine' => $this->getUltimoOrdine(),
        );
    }

    /**
     * Finds and displays a Licenza entity.
     *
     * @Route("/{gruppo}/{sigla}", name="catalogo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($gruppo, $sigla) {
        $entity = $this->findOneBy('JFCoreBundle:Licenza', array('gruppo' => $gruppo, 'sigla' => $sigla));

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
     * @Route("/{gruppo}/{sigla}/buy", name="catalogo_buy")
     * @Method("GET")
     * @Template()
     */
    public function buyLicenzaAction($gruppo, $sigla) {
        $entity = $this->findOneBy('JFCoreBundle:Licenza', array('gruppo' => $gruppo, 'sigla' => $sigla));

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
     * @Route("/buy", name="catalogo_carrello_buy")
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
                while ($licenza = $cliente->getLicenzaGruppo($prodotto->getLicenza()->getGruppo())) {
                    if ($licenza) {
                        $this->remove($licenza);
                    }
                }
                $_licenza = new \JF\ACLBundle\Entity\Licenza();
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
        $ordine = $this->findOneBy('JFCoreBundle:Ordine', array('cliente' => $this->getUser()->getCliente()->getId(), 'cancellazione' => null, 'ordinato' => null));

        if (!$ordine) {
            $ordine = new \JF\CoreBundle\Entity\Ordine();
            $ordine->setCliente($this->getUser()->getCliente());
            $this->persist($ordine);
        }

        return $ordine;
    }

}
