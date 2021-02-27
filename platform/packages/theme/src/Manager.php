<?php

namespace Botble\Theme;

class Manager
{
    /**
     * @var array
     */
    protected $themes = [];

    /**
     * Construct the class
     */
    public function __construct()
    {
        $this->registerTheme(self::getAllThemes());
    }

    /**
     * @return array
     */
    public function getAllThemes(): array
    {
        $themes = [];
        $themePath = theme_path();
        foreach (scan_folder($themePath) as $folder) {
            $theme = get_file_data($themePath . DIRECTORY_SEPARATOR . $folder . '/theme.json');
            if (!empty($theme)) {
                $themes[$folder] = $theme;
            }
        }
        return $themes;
    }

    /**
     * @param string|array $theme
     * @return void
     */
    public function registerTheme($theme): void
    {
        if (!is_array($theme)) {
            $theme = [$theme];
        }
        $this->themes = array_merge_recursive($this->themes, $theme);
    }

    /**
     * @return array
     */
    public function getThemes(): array
    {
        return $this->themes;
    }
}
