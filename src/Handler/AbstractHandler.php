<?php

namespace App\Handler;


use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

abstract class AbstractHandler
{
    /**
    * @var FormFactoryInterface
     */

    private $formFactory;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @return string
     */
    abstract protected function getForm() : string;

    /**
     * @param $data
     */
    abstract protected function process($data) : void;

    /**
     * @required
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactory(FormFactoryInterface $formFactory) :void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     * @param $data
     * @param array $options
     * @return bool
     */
    public function handle(Request $request, $data, array $options = []) :bool
    {
        $this->form = $this->formFactory->create($this->getForm(), $data, $options)->handleRequest($request);
        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->process($data);
            return true;
        }
        return false;
    }
    public function createView() :FormView
    {
        return $this->form->createView();
    }
}