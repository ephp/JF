<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;
use Ephp\UtilityBundle\Utility\String;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Claims\HAuditBundle\Entity\Pratica;
use Claims\HAuditBundle\Entity\Audit;
use Claims\HAuditBundle\Entity\Question;
use Claims\HAuditBundle\Form\AuditType;
use Claims\HAuditBundle\Form\QuestionType;

/**
 * Audit controller
 *
 * @Route("/claims-h-audit")
 */
class SwitchController extends Controller {

    use BaseController,
        Traits\AuditController;

    /**
     * Lists all Audit entities.
     *
     * @Route("/", name="claims-h-audit")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $questions = $this->findBy('ClaimsHAuditBundle:Question', array('somma' => true));
        $slc = array();
        foreach ($questions as $question) {
            /* @var $question Question */
            $q = "
select sum(replace(r.response, ',', '')) as tot,
       count(*) as n
  from claims_h_audit_pratica_question r 
 where r.question_id = :id
   and r.response != ''
";
            $rows = $this->executeSql($q, array('id' => $question->getId()));

            if ($rows[0]['n']) {
                $slc[] = array(
                    'q' => $question,
                    'tot' => $rows[0]['tot'],
                    'n' => $rows[0]['n'],
                );
            }
        }

        return array(
            'slc' => $slc,
        );
    }

}
