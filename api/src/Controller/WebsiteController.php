<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-17
 * Time: 15:54
 */

namespace App\Controller;

use App\Entity\Website;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class WebsiteController
 * @package App\Controller
 *
 * @Route("/api/websites")
 */
class WebsiteController extends ApiController
{
    private $entityManager;

    /**
     * WebsiteController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $websites = $this->entityManager->getRepository('App:Website')->findAll();

        return new JsonResponse($websites);
    }

    /**
     * @Route("/add", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $json = $this->getJsonContent($request);

        $website = new Website();
        $website->setName($json['name']);
        $website->setDescription($json['description']);
        $website->setRepo($json['repo']);

        $this->entityManager->persist($website);
        $this->entityManager->flush();

        $json['success'] = true;

        return new JsonResponse($json);
    }
}
