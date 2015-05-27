<?php

namespace Svi;

class AssetsInstallCommand extends ConsoleCommand
{

	public function getName()
	{
		return 'assets:install';
	}

	public function getDescription()
	{
		return 'Performs installation of bundles assets into web/s directory';
	}

	public function execute(array $args)
	{
		$assetsDir = $this->getApp()->getRootDir() . '/web/bundles';

		$this->writeLn('Cleaning assets dir: ' . $assetsDir . '...');
		$this->writeLn();

		array_map('unlink', glob($assetsDir . '/*'));

		$this->writeLn('Installing assets:');
		$this->writeLn();

		foreach ($this->getApp()->getBundles()->getBundles() as $b) {
			$dir = $b->getDir() . '/Public';
			$dest = $assetsDir . '/' . strtolower($b->getName());
			if (file_exists($dir)) {
				if (file_exists($dest)) {
					unlink($dest);
				}
				symlink($dir, $dest);
				$this->writeLn(str_replace($this->getApp()->getRootDir() . '/', '', $dir) . ' => ' .
					str_replace($this->getApp()->getRootDir() . '/', '', $dest));
			}
		}
	}

} 