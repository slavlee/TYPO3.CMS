<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Workspaces\Tests\Functional\DataHandling\Regular\Modify;

use TYPO3\CMS\Workspaces\Tests\Functional\DataHandling\Regular\AbstractActionTestCase;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\ResponseContent;

/**
 * Functional test for the DataHandler
 */
class ActionTest extends AbstractActionTestCase
{
    /**
     * @var string
     */
    protected $assertionDataSetDirectory = 'typo3/sysext/workspaces/Tests/Functional/DataHandling/Regular/Modify/DataSet/';

    /**
     * @test
     */
    public function verifyCleanReferenceIndex()
    {
        // The test verifies the imported data set has a clean reference index by the check in tearDown()
        self::assertTrue(true);
    }

    /**
     * @test
     */
    public function createContents()
    {
        parent::createContents();
        $this->assertAssertionDataSet('createContents');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Testing #1', 'Testing #2'));
    }

    /**
     * @test
     */
    public function createContentAndCopyContent()
    {
        parent::createContentAndCopyContent();
        $this->assertAssertionDataSet('createContentAndCopyContent');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Testing #1', 'Testing #1 (copy 1)'));
    }

    /**
     * @test
     */
    public function createContentAndLocalize()
    {
        parent::createContentAndLocalize();
        $this->assertAssertionDataSet('createContentAndLocalize');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageId, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Dansk:] Testing #1'));
    }

    /**
     * @test
     */
    public function modifyContent()
    {
        parent::modifyContent();
        $this->assertAssertionDataSet('modifyContent');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Testing #1'));
    }

    /**
     * @test
     */
    public function hideContent()
    {
        parent::hideContent();
        $this->assertAssertionDataSet('hideContent');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     */
    public function hideContentAndMoveToDifferentPage()
    {
        parent::hideContent();
        parent::moveContentToDifferentPage();
        $this->assertAssertionDataSet('hideContentAndMoveToDifferentPage');

        $responseSectionsSource = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsSource, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
        self::assertThat($responseSectionsSource, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
        $responseSectionsTarget = $this->getFrontendResponse(self::VALUE_PageIdTarget, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsTarget, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     */
    public function deleteContent()
    {
        parent::deleteContent();
        $this->assertAssertionDataSet('deleteContent');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
        self::assertThat($responseSections, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     */
    public function deleteLocalizedContentAndDeleteContent()
    {
        // Create translated page first
        $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageId);
        parent::deleteLocalizedContentAndDeleteContent();
        $this->assertAssertionDataSet('deleteLocalizedContentNDeleteContent');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageId, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #3', '[Translate to Dansk:] Regular Element #3', 'Regular Element #1'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Dansk:] Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function copyContent()
    {
        parent::copyContent();
        $this->assertAssertionDataSet('copyContent');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2 (copy 1)'));
    }

    /**
     * @test
     */
    public function copyContentToLanguage()
    {
        // Create translated page first
        $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageId);
        parent::copyContentToLanguage();
        $this->assertAssertionDataSet('copyContentToLanguage');

        // Set up "dk" to not have overlays
        $languageConfiguration = $this->siteLanguageConfiguration;
        $languageConfiguration[self::VALUE_LanguageId]['fallbackType'] = 'free';
        $this->setUpFrontendSite(1, $languageConfiguration);
        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageId, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Dansk:] Regular Element #3', '[Translate to Dansk:] Regular Element #2'));
    }

    /**
     * @test
     */
    public function copyContentToLanguageFromNonDefaultLanguage()
    {
        // Create translated page first
        $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageId);
        $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageIdSecond);
        parent::copyContentToLanguageFromNonDefaultLanguage();
        $this->assertAssertionDataSet('copyContentToLanguageFromNonDefaultLanguage');

        // Set up "de" to not have overlays
        $languageConfiguration = $this->siteLanguageConfiguration;
        $languageConfiguration[self::VALUE_LanguageIdSecond]['fallbackType'] = 'free';
        $this->setUpFrontendSite(1, $languageConfiguration);
        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageIdSecond, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Deutsch:] [Translate to Dansk:] Regular Element #3'));
    }

    /**
     * @test
     */
    public function localizeContent()
    {
        // Create translated page first
        $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageId);
        parent::localizeContent();
        $this->assertAssertionDataSet('localizeContent');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageId, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Dansk:] Regular Element #1', '[Translate to Dansk:] Regular Element #2'));
    }

    /**
     * @test
     * @see \TYPO3\CMS\Core\Migrations\TcaMigration::sanitizeControlSectionIntegrity()
     */
    public function localizeContentWithEmptyTcaIntegrityColumns()
    {
        parent::localizeContentWithEmptyTcaIntegrityColumns();
        $this->assertAssertionDataSet('localizeContentWithEmptyTcaIntegrityColumns');
        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageId, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Dansk:] Regular Element #1', '[Translate to Dansk:] Regular Element #2'));
    }

    /**
     * @test
     */
    public function localizeContentWithHideAtCopy()
    {
        // Create translated page first
        $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageId);
        parent::localizeContentWithHideAtCopy();
        $this->assertAssertionDataSet('localizeContentWHideAtCopy');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageId, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Dansk:] Regular Element #1', '[Translate to Dansk:] Regular Element #2'));
    }

    /**
     * @test
     */
    public function localizeContentFromNonDefaultLanguage()
    {
        // Create translated page first
        $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageId);
        $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageIdSecond);
        parent::localizeContentFromNonDefaultLanguage();

        $this->assertAssertionDataSet('localizeContentFromNonDefaultLanguage');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageIdSecond, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Deutsch:] [Translate to Dansk:] Regular Element #1', '[Translate to Deutsch:] [Translate to Dansk:] Regular Element #3'));
    }

    /**
     * @test
     */
    public function changeContentSorting()
    {
        parent::changeContentSorting();
        $this->assertAssertionDataSet('changeContentSorting');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function changeContentSortingAfterSelf()
    {
        parent::changeContentSortingAfterSelf();
        $this->assertAssertionDataSet('changeContentSortingAfterSelf');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     * @todo: Publish and PublishAll for this are missing - TF throws an exception on publish due to deleted state
     */
    public function changeContentSortingAndDeleteMovedRecord()
    {
        parent::changeContentSortingAndDeleteMovedRecord();
        $this->assertAssertionDataSet('changeContentSortingNDeleteMovedRecord');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function changeContentSortingAndDeleteLiveRecord()
    {
        parent::changeContentSortingAndDeleteLiveRecord();
        $this->assertAssertionDataSet('changeContentSortingNDeleteLiveRecord');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
        self::assertThat($responseSections, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
    }

    /**
     * @test
     */
    public function moveContentToDifferentPage()
    {
        parent::moveContentToDifferentPage();
        $this->assertAssertionDataSet('moveContentToDifferentPage');

        $responseSectionsSource = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsSource, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
        $responseSectionsTarget = $this->getFrontendResponse(self::VALUE_PageIdTarget, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsTarget, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     */
    public function moveContentToDifferentPageAndChangeSorting()
    {
        parent::moveContentToDifferentPageAndChangeSorting();
        $this->assertAssertionDataSet('moveContentToDifferentPageNChangeSorting');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageIdTarget, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function moveContentToDifferentPageAndHide()
    {
        parent::moveContentToDifferentPageAndHide();
        $this->assertAssertionDataSet('moveContentToDifferentPageAndHide');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageIdTarget, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * Page records
     */

    /**
     * @test
     */
    public function createPage()
    {
        parent::createPage();
        $this->assertAssertionDataSet('createPage');

        $responseSections = $this->getFrontendResponse($this->recordIds['newPageId'], 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Testing #1'));
    }

    /**
     * @test
     */
    public function createPageAndSubPageAndSubPageContent()
    {
        parent::createPageAndSubPageAndSubPageContent();
        $this->assertAssertionDataSet('createPageAndSubPageAndSubPageContent');

        $responseSections = $this->getFrontendResponse($this->recordIds['newSubPageId'], 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Testing #1 #1'));
    }

    /**
     * @test
     */
    public function createPageAndContentWithTcaDefaults()
    {
        parent::createPageAndContentWithTcaDefaults();
        $this->assertAssertionDataSet('createPageNContentWDefaults');

        // first, assert that page cannot be opened without using backend user (since it's hidden)
        $response = $this->executeFrontendRequest(
            (new InternalRequest())
                ->withPageId($this->recordIds['newPageId'])
        );
        self::assertSame(404, $response->getStatusCode());

        // then, assert if preview is possible using a backend user
        $response = $this->executeFrontendRequest(
            (new InternalRequest())
                ->withPageId($this->recordIds['newPageId']),
            (new InternalRequestContext())
                ->withBackendUserId(self::VALUE_BackendUserId)
                ->withWorkspaceId(self::VALUE_WorkspaceId)
        );
        $responseSections = ResponseContent::fromString((string)$response->getBody())
            ->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Testing #1'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Testing #1'));
    }

    /**
     * @test
     */
    public function modifyPage()
    {
        parent::modifyPage();
        $this->assertAssertionDataSet('modifyPage');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Testing #1'));
    }

    /**
     * @test
     */
    public function deletePage()
    {
        parent::deletePage();
        $this->assertAssertionDataSet('deletePage');

        $response = $this->executeFrontendRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageId),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)->withWorkspaceId(self::VALUE_WorkspaceId)
        );
        self::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function deleteContentAndPage()
    {
        parent::deleteContentAndPage();
        $this->assertAssertionDataSet('deleteContentAndPage');

        $response = $this->executeFrontendRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageId),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)->withWorkspaceId(self::VALUE_WorkspaceId)
        );
        self::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     * Publish tests missing since TF throws exception if publishing deleted records
     */
    public function localizePageAndContentsAndDeletePageLocalization()
    {
        // Create localized page and localize content elements first
        parent::localizePageAndContentsAndDeletePageLocalization();
        $this->assertAssertionDataSet('localizePageAndContentsAndDeletePageLocalization');

        $response = $this->executeFrontendRequest(
            (new InternalRequest())->withPageId($this->recordIds['localizedPageId']),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)->withWorkspaceId(self::VALUE_WorkspaceId)
        );
        self::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function localizeNestedPagesAndContents()
    {
        parent::localizeNestedPagesAndContents();
        $this->assertAssertionDataSet('localizeNestedPagesAndContents');
    }

    /**
     * @test
     */
    public function copyPage()
    {
        parent::copyPage();
        $this->assertAssertionDataSet('copyPage');

        $responseSections = $this->getFrontendResponse($this->recordIds['newPageId'], 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
    }

    /**
     * @test
     */
    public function copyPageFreeMode()
    {
        parent::copyPageFreeMode();
        $this->assertAssertionDataSet('copyPageFreeMode');

        $responseSections = $this->getFrontendResponse($this->recordIds['newPageId'], 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Target'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #10'));
    }

    /**
     * @test
     */
    public function localizePage()
    {
        parent::localizePage();
        $this->assertAssertionDataSet('localizePage');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageId, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('[Translate to Dansk:] Relations'));
    }

    /**
     * @test
     */
    public function createPageAndChangePageSorting()
    {
        parent::createPageAndChangePageSorting();
        $this->assertAssertionDataSet('createPageAndChangePageSorting');
    }

    /**
     * @test
     */
    public function createPageAndMoveCreatedPage()
    {
        parent::createPageAndMoveCreatedPage();
        $this->assertAssertionDataSet('createPageAndMoveCreatedPage');
    }

    /**
     * @test
     */
    public function changePageSorting()
    {
        parent::changePageSorting();
        $this->assertAssertionDataSet('changePageSorting');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function changePageSortingAfterSelf()
    {
        parent::changePageSortingAfterSelf();
        $this->assertAssertionDataSet('changePageSortingAfterSelf');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function movePageToDifferentPage()
    {
        parent::movePageToDifferentPage();
        $this->assertAssertionDataSet('movePageToDifferentPage');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function movePageToDifferentPageTwice()
    {
        parent::movePageToDifferentPageTwice();
        $this->assertAssertionDataSet('movePageToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageLocalizedToDifferentPageTwice()
    {
        parent::movePageLocalizedToDifferentPageTwice();
        $this->assertAssertionDataSet('movePageLocalizedToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageLocalizedInLiveToDifferentPageTwice()
    {
        parent::movePageLocalizedInLiveToDifferentPageTwice();
        $this->assertAssertionDataSet('movePageLocalizedInLiveToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageLocalizedInLiveWorkspaceChangedToDifferentPageTwice()
    {
        parent::movePageLocalizedInLiveWorkspaceChangedToDifferentPageTwice();
        $this->assertAssertionDataSet('movePageLocalizedInLiveWorkspaceChangedToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageLocalizedInLiveWorkspaceDeletedToDifferentPageTwice()
    {
        parent::movePageLocalizedInLiveWorkspaceDeletedToDifferentPageTwice();
        $this->assertAssertionDataSet('movePageLocalizedInLiveWorkspaceDeletedToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageToDifferentPageAndChangeSorting()
    {
        parent::movePageToDifferentPageAndChangeSorting();
        $this->assertAssertionDataSet('movePageToDifferentPageNChangeSorting');

        $responseSectionsPage = $this->getFrontendResponse(self::VALUE_PageId, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsPage, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
        self::assertThat($responseSectionsPage, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
        $responseSectionsWebsite = $this->getFrontendResponse(self::VALUE_PageIdWebsite, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsWebsite, $this->getRequestSectionStructureHasRecordConstraint()
            ->setRecordIdentifier(self::TABLE_Page . ':' . self::VALUE_PageIdWebsite)->setRecordField('__pages')
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Target', 'Relations', 'DataHandlerTest'));
    }

    /**
     * @test
     * @see https://forge.typo3.org/issues/33104
     * @see https://forge.typo3.org/issues/55573
     */
    public function movePageToDifferentPageAndCreatePageAfterMovedPage()
    {
        parent::movePageToDifferentPageAndCreatePageAfterMovedPage();
        $this->assertAssertionDataSet('movePageToDifferentPageNCreatePageAfterMovedPage');

        $responseSections = $this->getFrontendResponse(self::VALUE_PageIdWebsite, 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSections, $this->getRequestSectionStructureHasRecordConstraint()
            ->setRecordIdentifier(self::TABLE_Page . ':' . self::VALUE_PageIdWebsite)->setRecordField('__pages')
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Target', 'Testing #1', 'DataHandlerTest'));
    }

    /*************************************
     * Copying page contents and sub-pages
     *************************************/

    /**
     * @test
     */
    public function createContentAndCopyDraftPage()
    {
        parent::createContentAndCopyDraftPage();
        $this->assertAssertionDataSet('createContentAndCopyDraftPage');

        $resultLive = $this->getFrontendResult($this->recordIds['copiedPageId']);
        self::assertStringContainsString('The requested page does not exist', $resultLive['stdout']);
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionHasRecordConstraint()
            ->setTable(static::TABLE_Content)->setField('header')->setValues('Testing #1'));
    }

    /**
     * @test
     * Test does not make sense for Publish, PublishAll and Discard
     */
    public function createContentAndCopyLivePage()
    {
        parent::createContentAndCopyLivePage();
        $this->assertAssertionDataSet('createContentAndCopyLivePage');

        $responseSectionsLive = $this->getFrontendResponse($this->recordIds['copiedPageId'])->getResponseSections();
        self::assertThat($responseSectionsLive, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(static::TABLE_Content)->setField('header')->setValues('Testing #1'));
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(static::TABLE_Content)->setField('header')->setValues('Testing #1'));
    }

    /**
     * @test
     */
    public function createPageAndCopyDraftParentPage()
    {
        parent::createPageAndCopyDraftParentPage();
        $this->assertAssertionDataSet('createPageAndCopyDraftParentPage');

        $resultLive = $this->getFrontendResult($this->recordIds['copiedPageId']);
        self::assertStringContainsString('The requested page does not exist', $resultLive['stdout']);
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionHasRecordConstraint()
            ->setTable(static::TABLE_Page)->setField('title')->setValues('Testing #1'));
    }

    /**
     * @test
     * Test does not make sense for Publish, PublishAll and Discard
     */
    public function createPageAndCopyLiveParentPage()
    {
        parent::createPageAndCopyLiveParentPage();
        $this->assertAssertionDataSet('createPageAndCopyLiveParentPage');

        $responseSectionsLive = $this->getFrontendResponse($this->recordIds['copiedPageId'])->getResponseSections();
        self::assertThat($responseSectionsLive, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(static::TABLE_Page)->setField('title')->setValues('Testing #1'));
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, self::VALUE_BackendUserId, self::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(static::TABLE_Page)->setField('title')->setValues('Testing #1'));
    }

    /**
     * @test
     * Skipping test in Publish but it is available in PublishAll
     */
    public function createNestedPagesAndCopyDraftParentPage()
    {
        parent::createNestedPagesAndCopyDraftParentPage();
        $this->assertAssertionDataSet('createNestedPagesAndCopyDraftParentPage');

        $resultLive = $this->getFrontendResult($this->recordIds['copiedPageId']);
        self::assertStringContainsString('The requested page does not exist', $resultLive['stdout']);
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, static::VALUE_BackendUserId, static::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionHasRecordConstraint()
            ->setTable(static::TABLE_Page)->setField('title')->setValues('Testing #1'));
    }

    /**
     * @test
     * Test does not make sense for Publish, PublishAll and Discard
     */
    public function createNestedPagesAndCopyLiveParentPage()
    {
        parent::createNestedPagesAndCopyLiveParentPage();
        $this->assertAssertionDataSet('createNestedPagesAndCopyLiveParentPage');

        $responseSectionsLive = $this->getFrontendResponse($this->recordIds['copiedPageId'])->getResponseSections();
        self::assertThat($responseSectionsLive, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(static::TABLE_Page)->setField('title')->setValues('Testing #1'));
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, static::VALUE_BackendUserId, static::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(static::TABLE_Page)->setField('title')->setValues('Testing #1'));
    }

    /**
     * @test
     * Skipped test in Publish, PublishAll and Discard: It's only interesting if the copy operation copies the deleted record
     */
    public function deleteContentAndCopyDraftPage()
    {
        parent::deleteContentAndCopyDraftPage();
        $this->assertAssertionDataSet('deleteContentAndCopyDraftPage');

        $resultLive = $this->getFrontendResult($this->recordIds['copiedPageId']);
        self::assertStringContainsString('The requested page does not exist', $resultLive['stdout']);
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, static::VALUE_BackendUserId, static::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     * Skipped test in Publish, PublishAll and Discard: It's only interesting if the copy operation copies the deleted record
     */
    public function deleteContentAndCopyLivePage()
    {
        parent::deleteContentAndCopyLivePage();
        $this->assertAssertionDataSet('deleteContentAndCopyLivePage');

        $responseSectionsLive = $this->getFrontendResponse($this->recordIds['copiedPageId'])->getResponseSections();
        self::assertThat($responseSectionsLive, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, static::VALUE_BackendUserId, static::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     * Test skipped in publish and discard, but exists in PublishAll
     */
    public function changeContentSortingAndCopyDraftPage()
    {
        parent::changeContentSortingAndCopyDraftPage();
        $this->assertAssertionDataSet('changeContentSortingAndCopyDraftPage');

        $resultLive = $this->getFrontendResult($this->recordIds['copiedPageId']);
        self::assertStringContainsString('The requested page does not exist', $resultLive['stdout']);
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, static::VALUE_BackendUserId, static::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
    }

    /**
     * @test
     * Test does not make sense for Publish, PublishAll and Discard
     */
    public function changeContentSortingAndCopyLivePage()
    {
        parent::changeContentSortingAndCopyLivePage();
        $this->assertAssertionDataSet('changeContentSortingAndCopyLivePage');

        $responseSectionsLive = $this->getFrontendResponse($this->recordIds['copiedPageId'])->getResponseSections();
        self::assertThat($responseSectionsLive, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, static::VALUE_BackendUserId, static::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
    }

    /**
     * @test
     * Skipped test in Publish, PublishAll and Discard: It's only interesting if the move operation does sane things
     */
    public function moveContentAndCopyDraftPage()
    {
        parent::moveContentAndCopyDraftPage();
        $this->assertAssertionDataSet('moveContentAndCopyDraftPage');

        $resultLive = $this->getFrontendResult($this->recordIds['copiedPageId']);
        self::assertStringContainsString('The requested page does not exist', $resultLive['stdout']);
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, static::VALUE_BackendUserId, static::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
        self::assertThat($responseSectionsDraft, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #0'));
    }

    /**
     * @test
     * Test does not make sense for Publish, PublishAll and Discard
     */
    public function moveContentAndCopyLivePage()
    {
        parent::moveContentAndCopyLivePage();
        $this->assertAssertionDataSet('moveContentAndCopyLivePage');

        $responseSectionsLive = $this->getFrontendResponse($this->recordIds['copiedPageId'])->getResponseSections();
        self::assertThat($responseSectionsLive, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
        self::assertThat($responseSectionsLive, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #0'));
        $responseSectionsDraft = $this->getFrontendResponse($this->recordIds['copiedPageId'], 0, static::VALUE_BackendUserId, static::VALUE_WorkspaceId)->getResponseSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
        self::assertThat($responseSectionsDraft, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #0'));
    }

    /**
     * @test
     */
    public function createPlaceholdersAndDeleteDraftParentPage()
    {
        parent::createPlaceholdersAndDeleteDraftParentPage();
        $this->assertAssertionDataSet('createPlaceholdersAndDeleteDraftParentPage');
    }

    /**
     * @test
     * Test does not make much sense in Publish and Discard and is skipped there
     */
    public function createPlaceholdersAndDeleteLiveParentPage()
    {
        parent::createPlaceholdersAndDeleteLiveParentPage();
        $this->assertAssertionDataSet('createPlaceholdersAndDeleteLiveParentPage');
    }
}
