<?php

namespace FSi\Bundle\AdminTranslatableBundle\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class ListContext extends PageObjectContext
{
    /**
     * @Given /^I click "([^"]*)" in "([^"]*)" column in third row$/
     */
    public function iClickInColumnInThirdRow($linkName, $columnName)
    {
        /** @var \FSi\Bundle\AdminTranslatableBundle\Behat\Context\Page\Element\Grid $grid */
        $grid = $this->getElement('Grid');
        $cell = $grid->getCell(3, $grid->getColumnPosition($columnName));
        $cell->findLink($linkName)->click();
    }

    /**
     * @Then /^I should see following list$/
     */
    public function iShouldSeeFollowingList(TableNode $table)
    {
        /** @var \FSi\Bundle\AdminTranslatableBundle\Behat\Context\Page\Element\Grid $grid */
        $grid = $this->getElement('Grid');

        expect(count($table->getHash()))->toBe($grid->getRowsCount());

        $rowNumber = 1;
        foreach ($table->getHash() as $row) {
            foreach ($row as $columnName => $cellValue) {
                $cell = $grid->getCell($rowNumber, $grid->getColumnPosition($columnName));

                expect($cell->getText())->toBe($cellValue);
            }

            $rowNumber++;
        }
    }
}
