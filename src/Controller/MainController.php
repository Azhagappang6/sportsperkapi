<?php

namespace App\Controller;
// 
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request as HtttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// 
use App\Services\HKContentService as CTS;

// 
/**
 * Root Controller.
 *
 * @Route("api/")
 */
class MainController extends AbstractController
{
    /** 
     * ExecutePost
     * @FOSRest\Post("{controller}/{action}/{typeOrId}")
     * 
     * @return array
     */
    public function ExecutePost(HtttpRequest $request, $controller, $action, $typeOrId = null)
    {
        $contentService = new CTS($this, $request);
        $response = $contentService->Execute($controller, $action, $typeOrId);
        return View::create($response, HttpResponse::HTTP_OK, []);
    }

    /**
     * 
     */
    public function GetConnection()
    {
        return $this->getDoctrine()->getManager()->getConnection();
    }
}
