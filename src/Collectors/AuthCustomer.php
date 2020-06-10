<?php

namespace Adnduweb\Ci4_ecommerce\Collectors;

/**
 * Auth collector
 */
class Authcustomer extends \CodeIgniter\Debug\Toolbar\Collectors\BaseCollector
{

	/**
	 * Whether this collector has data that can
	 * be displayed in the Timeline.
	 *
	 * @var boolean
	 */
	protected $hasTimeline = false;

	/**
	 * Whether this collector needs to display
	 * content in a tab or not.
	 *
	 * @var boolean
	 */
	protected $hasTabContent = true;

	/**
	 * Whether this collector has data that
	 * should be shown in the Vars tab.
	 *
	 * @var boolean
	 */
	protected $hasVarData = false;

	/**
	 * The 'title' of this Collector.
	 * Used to name things in the toolbar HTML.
	 *
	 * @var string
	 */
	protected $title = 'Customer';

	//--------------------------------------------------------------------

	/**
	 * Returns any information that should be shown next to the title.
	 *
	 * @return string
	 */
	public function getTitleDetails(): string
	{
		return '';
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the timeline data formatted for correct usage.
	 *
	 * @return string
	 */
	protected function formatTimelineData(): array
	{
		$data = [];
		/*
		$rows = $this->viewer->getPerformanceData();

		foreach ($rows as $name => $info)
		{
			$data[] = [
				'name'      => 'View: ' . $info['view'],
				'component' => 'Views',
				'start'     => $info['start'],
				'duration'  => $info['end'] - $info['start'],
			];
		}
*/
		return $data;
	}

	/**
	 * Returns the data of this collector to be formatted in the toolbar
	 *
	 * @return string
	 */
	public function display(): string
	{
		$authenticateCustomer = service('authenticationcustomer');
		// print_r($authenticateCustomer);
		// exit;
		if ($authenticateCustomer->isLoggedIn()) {
			$customer = $authenticateCustomer->customer();


			/**
			 *  Should groups be added here,
			 *  as an afterFind action in UserModel
			 *  so it's available globally,
			 *  or not at all?
			 */
			$groupModel = new \Adnduweb\Ci4_ecommerce\Models\GroupModel();
			$customer->groups =  $groupModel->getGroupsForCustomer($customer->id);

			$groupsForCustomer = '';

			if (!empty($customer->groups)) {
				foreach ($customer->groups as $group) {
					$groupsForCustomer .= $group['name'] . ', ';
				}
			}

			$html = '<h3>Current Customer</h3>';
			$html .= '<table><tbody>';
			$html .= "<tr><td style='width:150px;'>Customer ID</td><td>#{$customer->id}</td></tr>";
			$html .= "<tr><td style='width:150px;'>Customer UUID</td><td>#{$customer->uuid}</td></tr>";
			$html .= "<tr><td>Username</td><td>{$customer->firstname} {$customer->lastname}</td></tr>";
			$html .= "<tr><td>Email</td><td>{$customer->email}</td></tr>";
			$html .= "<tr><td>Groups</td><td>{$groupsForCustomer}</td></tr>";
			$html .= '</tbody></table>';
		} else {
			$html = '<p>Not logged in.</p>';
		}
		return $html;
	}

	//--------------------------------------------------------------------

	/**
	 * Gets the "badge" value for the button.
	 *
	 * @return integer
	 */
	public function getBadgeValue(): int
	{
		$authenticateCustomer = service('authenticationcustomer');
		// print_r($authenticateCustomer);
		// exit;
		if ($authenticateCustomer->isLoggedIn()) {
			return 1;
		}
		return 0;
	}

	//--------------------------------------------------------------------

	/**
	 * Display the icon.
	 *
	 * Icon from https://icons8.com - 1em package
	 *
	 * @return string
	 */
	public function icon(): string
	{
		return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAADLSURBVEhL5ZRLCsIwGAa7UkE9gd5HUfEoekxxJx7AhXoCca/fhESkJiQxBHwMDG3S/9EmJc0n0JMruZVXK/fMdWQRY7mXt4A7OZJvwZu74hRayIEc2nv3jGtXZrOWrnifiRY0OkhiWK5sWGeS52bkZymJ2ZhRJmwmySxLCL6CmIsZZUIixkiNezCRR+kSUyWH3Cgn6SuQIk2iuOBckvN+t8FMnq1TJloUN3jefN9mhvJeCAVWb8CyUDj0vxc3iPFHDaofFdUPu2+iae7nYJMCY/1bpAAAAABJRU5ErkJggg==';
	}
}
