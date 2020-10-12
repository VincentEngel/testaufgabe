<?php

declare(strict_types = 1);

namespace App\Controller;


use App\Entity\Userfile;
use App\Form\FileRenameFormType;
use App\Form\FileUploadFormType;
use App\Util\FileInfo;
use App\Service\FileRemover;
use App\Service\FileRenamer;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class FilesController extends AbstractController
{
    public function index(): Response
    {
        return $this->render("files/index.html.twig");
    }

    public function upload(Request $request): Response
    {
        return $this->render("files/upload.html.twig", [
            "msg" => $request->query->get("msg"),
            'uploadFileForm' => $this->createForm(
                FileUploadFormType::class,
                null,
                [
                    'action' => $this->generateUrl("files_new")
                ]
            )->createView(),
        ]);
    }

    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $userFile = new Userfile();
        $form = $this->createForm(FileUploadFormType::class, $userFile);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return new RedirectResponse($this->generateUrl("files_upload", ["msg" => 'Uploading your file failed.']));
        }

        $fileUploader = new FileUploader(
            $entityManager = $this->getDoctrine()->getManager(),
            $this->getParameter('file_path'),
            $slugger
        );

        $msg = $fileUploader->upload($form->get('file')->getData(), $this->getUser());

        return new RedirectResponse($this->generateUrl("files_upload", ["msg" => $msg]));
    }

    public function view(Request $request): Response
    {
        $userFiles = $this->getDoctrine()->getRepository(Userfile::class)->findBy([
            'owner' => $this->getUser()->getId(),
        ]);

        $filesInfo = [];

        /** @var Userfile $userFile */
        foreach ($userFiles as $userFile) {
            $filesInfo[] = [
                'name' => FileInfo::getFullFileName($userFile),
                'fileSize' => FileInfo::getFormattedFileSize($userFile->getFileSize()),
                'downloadUrl' => $this->generateUrl("files_download", ["file_id" => $userFile->getId()]),
                'renameUrl' => $this->generateUrl("files_detail", ["file_id" => $userFile->getId()]),
                'deleteUrl'=> $this->generateUrl("files_delete", ["file_id" => $userFile->getId()]),
            ];
        }
        return $this->render("files/view.html.twig", [
            'files' => $filesInfo,
            'msg' => $request->query->get('msg'),
        ]);
    }

    public function download(Request $request): Response
    {
        /** @var Userfile $userFile */
        $userFile = $this->getDoctrine()->getRepository(Userfile::class)->findOneBy([
            'id' => $request->query->get('file_id'),
            'owner' => $this->getUser()->getId(),
        ]);

        return $this->file(
            FileInfo::getFullFilePath($userFile),
            FileInfo::getFullFileName($userFile)
        );
    }

    public function delete(Request $request): Response
    {
        /** @var Userfile $userFile */
        $userFile = $this->getDoctrine()->getRepository(Userfile::class)->findOneBy([
            'id' => $request->query->get('file_id'),
            'owner' => $this->getUser()->getId(),
        ]);

        $fileRemover = new FileRemover(new Filesystem(), $this->getDoctrine()->getManager());

        $msg = $fileRemover->delete($userFile);

        return new RedirectResponse($this->generateUrl("files_view", ["msg" => $msg]));
    }

    public function rename(Request $request, SluggerInterface $slugger): Response
    {
        $renamedUserFile = new Userfile();

        $form = $this->createForm(FileRenameFormType::class, $renamedUserFile);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return new RedirectResponse($this->generateUrl("files_detail", [
                "msg" => 'Renaming your file failed.',
                'file_id' => $request->query->get('file_id'),
            ]));
        }

        /** @var Userfile $userFile */
        $userFile = $this->getDoctrine()->getRepository(Userfile::class)->findOneBy([
            'id' => $renamedUserFile->getId(),
            'owner' => $this->getUser()->getId(),
        ]);

        $fileRenamer = new FileRenamer(new Filesystem(), $this->getDoctrine()->getManager(), $slugger);

        $msg = $fileRenamer->rename($userFile, $renamedUserFile->getName());

        return new RedirectResponse($this->generateUrl("files_detail", [
            "msg" => $msg,
            'file_id' => $userFile->getId(),
        ]));
    }

    public function detail(Request $request): Response
    {
        /** @var Userfile $userFile */
        $userFile = $this->getDoctrine()->getRepository(Userfile::class)->findOneBy([
            'id' => $request->query->get('file_id'),
            'owner' => $this->getUser()->getId(),
        ]);

        return $this->render("files/detail.html.twig", [
            'name' => FileInfo::getFullFileName($userFile),
            'fileSize' => FileInfo::getFormattedFileSize($userFile->getFileSize()),
            'msg' => $request->query->get('msg'),
            'renameFileForm' => $this->createForm(
                FileRenameFormType::class,
                null,
                [
                    'action' => $this->generateUrl("files_rename"),
                    'data' => $userFile,
                ]
            )->createView(),
        ]);
    }
}