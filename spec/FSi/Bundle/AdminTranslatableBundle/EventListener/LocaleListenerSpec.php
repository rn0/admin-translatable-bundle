<?php

namespace spec\FSi\Bundle\AdminTranslatableBundle\EventListener;

use FSi\Bundle\AdminTranslatableBundle\Manager\LocaleManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListenerSpec extends ObjectBehavior
{
    function let(LocaleManager $localeManager)
    {
        $this->beConstructedWith($localeManager);
    }

    function it_implements_Event_Subscriber_Interface()
    {
        $this->beAnInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subscribes_kernel_request_event()
    {
        $this->getSubscribedEvents()->shouldReturn(
            array(
                KernelEvents::REQUEST => 'onKernelRequest',
            )
        );
    }

    function it_does_nothing_if_request_is_not_master(
        GetResponseEvent $event,
        Request $request,
        LocaleManager $localeManager)
    {
        $event->getRequest()->willReturn($request);
        $request->get('locale')->willReturn(null);

        $localeManager->setLocale(Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_sets_locale_to_locale_manager(
        GetResponseEvent $event,
        Request $request,
        LocaleManager $localeManager
    ) {
        $event->getRequest()->willReturn($request);
        $request->get('locale')->willReturn('pl');
        $localeManager->setLocale('pl')->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
