<?php
namespace Visol\EasyvoteEducation\Command;

/**
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

class PanelCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

	/**
	 * @var \Visol\EasyvoteEducation\Domain\Repository\PanelRepository
	 * @inject
	 */
	protected $panelRepository;

	/**
	 * persistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * One day after a panel, an e-mail requesting for feedback needs to be sent
	 * to the teacher and all attending politicians
	 *
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
	 */
	public function sendFeedbackEmailForPanelsCommand() {
		$pastPanels = $this->panelRepository->findPastPanelsWithoutFeedbackEmailSent();
		$this->outputLine('Past panels that need a feedback mail: ' . count($pastPanels));
		$surveyLink = 'http://www.easyvoteeducationfeedback.ch';
		foreach ($pastPanels as $pastPanel) {
			/** @var $pastPanel \Visol\EasyvoteEducation\Domain\Model\Panel */
			$this->outputLine('Past panel: ' . $pastPanel->getDate()->format('Y-m-d') . ' | ' . $pastPanel->getTitle());

			// Send feedback e-mail to teacher
			$this->outputLine('Sending mail to teacher: ' . $pastPanel->getCommunityUser()->getEmail());
			/** @var \Visol\Easyvote\Service\TemplateEmailService $templateEmail */
			$templateEmail = $this->objectManager->get('Visol\Easyvote\Service\TemplateEmailService');
			$templateEmail->addRecipient($pastPanel->getCommunityUser());
			$templateEmail->setTemplateName('panelFeedbackTeacher');
			$templateEmail->setExtensionName('easyvoteeducation');
			$templateEmail->assign('surveyLink', $surveyLink);
			$templateEmail->enqueue();

			foreach ($pastPanel->getPanelInvitations() as $panelInvitation) {
				/** @var $panelInvitation \Visol\EasyvoteEducation\Domain\Model\PanelInvitation */
				if ($panelInvitation->getAttendingCommunityUser() instanceof \Visol\Easyvote\Domain\Model\CommunityUser) {
					$this->outputLine('Sending mail to politician: ' . $panelInvitation->getAttendingCommunityUser()->getEmail());
					// Send feedback e-mail to politician
					/** @var \Visol\Easyvote\Service\TemplateEmailService $templateEmail */
					$templateEmail = $this->objectManager->get('Visol\Easyvote\Service\TemplateEmailService');
					$templateEmail->addRecipient($panelInvitation->getAttendingCommunityUser());
					$templateEmail->setTemplateName('panelFeedbackPolitician');
					$templateEmail->setExtensionName('easyvoteeducation');
					$templateEmail->assign('surveyLink', $surveyLink);
					$templateEmail->enqueue();
				}
			}
			$pastPanel->setFeedbackMailSent(TRUE);
			$this->panelRepository->update($pastPanel);
			$this->persistenceManager->persistAll();
		}
	}

	/**
	 * One week, two weeks and one month before a panel, the teacher organizing the panel
	 * is reminded of the upcoming panel.
	 *
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
	 */
	public function sendReminderForPanelsCommand() {
		$reminders = array('oneweek', 'twoweeks', 'onemonth');

		foreach ($reminders as $reminder) {
			$affectedPanels = $this->panelRepository->findPanelsWithinDateConstraintWithoutReminderEmailSent($reminder);
			$this->outputLine('Panels due in ' . $reminder . ': ' . count($affectedPanels));
			foreach ($affectedPanels as $panel) {
				/** @var $panel \Visol\EasyvoteEducation\Domain\Model\Panel */
				$this->outputLine($panel->getDate()->format('Y-m-d') . ' | ' . $panel->getTitle() . ' | Sending reminder to ' . $panel->getCommunityUser()->getEmail());

				// Send reminder e-mail to teacher
				/** @var \Visol\Easyvote\Service\TemplateEmailService $templateEmail */
				$templateEmail = $this->objectManager->get('Visol\Easyvote\Service\TemplateEmailService');
				$templateEmail->addRecipient($panel->getCommunityUser());
				$templateEmail->setTemplateName('panelReminder' . ucfirst($reminder) . 'Teacher');
				$templateEmail->setExtensionName('easyvote_education');
				$templateEmail->assign('panel', $panel);
				$templateEmail->enqueue();

				$setter = 'setReminder' . ucfirst($reminder) . 'Sent';
				$panel->$setter(TRUE);
				$this->panelRepository->update($panel);
				$this->persistenceManager->persistAll();
			}
		}
	}

}
