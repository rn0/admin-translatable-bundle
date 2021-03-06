<?php

namespace spec\FSi\Bundle\AdminTranslatableBundle\Menu;

use FSi\Bundle\AdminTranslatableBundle\Manager\LocaleManager;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class KnpMenuTranslatableElementVoterSpec extends ObjectBehavior
{
    function let(VoterInterface $menuElementVoter, LocaleManager $localeManager)
    {
        $this->beConstructedWith($menuElementVoter, $localeManager);
    }

    function it_should_pass_through_if_inner_voter_cant_decide(VoterInterface $menuElementVoter, ItemInterface $item)
    {
        $menuElementVoter->matchItem($item)->willReturn(null);

        $this->matchItem($item)->shouldReturn(null);
    }

    function it_should_correctly_match_the_same_elements_with_different_locale(
        VoterInterface $menuElementVoter,
        ItemInterface $item,
        LocaleManager $localeManager
    ) {
        $localeManager->getLocale()->willReturn('pl');

        $menuElementVoter->matchItem($item)->willReturn(true);

        $item->getExtra('routes', array())->willReturn(array(
            0 => array('parameters' => array('locale' => 'en'))
        ));

        $this->matchItem($item)->shouldReturn(false);

        $item->getExtra('routes', array())->willReturn(array(
            0 => array('parameters' => array('locale' => 'pl'))
        ));

        $this->matchItem($item)->shouldReturn(true);

        $item->getExtra('routes', array())->willReturn(array(
            0 => array('parameters' => array('locale' => 'de'))
        ));

        $this->matchItem($item)->shouldReturn(false);
    }
}
