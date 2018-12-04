<?php

namespace App\Controller;

use App\Entity\SchoolClass;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

use App\Entity\UserTeacher;

class UserTeacherController extends AbstractController
{
    /**
     * @SWG\Tag(name="Teacher")
     * @SWG\Response(
     *     response="200",
     *     description="Return a collection of teachers",
     *     schema=@SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=UserTeacher::class, groups={"user_list", "teacher_list"}))
     *     )
     * )
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTeachersAction()
    {
        $teachers = $this->getDoctrine()->getRepository(UserTeacher::class)->findAll();

        return $this->resSuccess($teachers, ['user_list', 'teacher_list']);
    }

    /**
     * @SWG\Tag(name="Teacher")
     * @SWG\Response(
     *     response="200",
     *     description="Return a teacher item",
     *     schema=@SWG\Schema(
     *          type="object",
     *          ref=@Model(type=UserTeacher::class, groups={"user_item", "teacher_item"})
     *     )
     * )
     *
     * @param int $teacherId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTeacherAction(int $teacherId)
    {
        $teacher = $this->getDoctrine()->getRepository(UserTeacher::class)->find($teacherId);

        return $this->resSuccess($teacher, ['user_item', 'teacher_item']);
    }

    /**
     * @ParamConverter("class", converter="fos_rest.request_body")
     *
     * @param int $teacherId
     * @param SchoolClass $class
     * @param ConstraintViolationListInterface $violations
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postTeacherClassAction(
        int $teacherId,
        SchoolClass $class,
        ConstraintViolationListInterface $violations
    ) {
        if (count($violations) > 0) return $this->resSuccess($violations, [], Response::HTTP_BAD_REQUEST);

        /** @var UserTeacher $teacher */
        $teacher = $this->getDoctrine()->getRepository(UserTeacher::class)->find($teacherId);
        $teacher->addSchoolClass($class);

        $this->getDoctrine()->getManager()->persist($teacher);
        $this->getDoctrine()->getManager()->flush();

        return $this->resSuccess($teacher, ['teacher_item'], Response::HTTP_CREATED);
    }

    /**
     * @param int $teacherId
     * @param int $classId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteTeacherClassAction(
        int $teacherId,
        int $classId
    ) {
        /** @var UserTeacher $teacher */
        $teacher = $this->getDoctrine()->getRepository(UserTeacher::class)->find($teacherId);
        /** @var SchoolClass $class */
        $class = $this->getDoctrine()->getRepository(SchoolClass::class)->find($classId);

        $teacher->removeSchoolClass($class);

        $this->getDoctrine()->getManager()->persist($teacher);
        $this->getDoctrine()->getManager()->flush();

        return $this->resSuccess($teacher, ['teacher_item'], Response::HTTP_CREATED);
    }
}