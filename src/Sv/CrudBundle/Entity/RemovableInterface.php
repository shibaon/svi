<?php

namespace Sv\CrudBundle\Entity;

interface RemovableInterface
{

	/**
	 * @return bool
	 */
	public function getRemoved();

	/**
	 * @param bool $removed
	 * @return
	 */
	public function setRemoved($removed);

	public function remove();

}
