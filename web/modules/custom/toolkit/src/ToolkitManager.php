<?php

namespace Drupal\toolkit;

use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Toolkit Manager.
 */
class ToolkitManager {

  /**
   * The active menu trail service.
   */
  protected MenuActiveTrailInterface $menuActiveTrail;

  /**
   * The menu link tree manager.
   */
  protected MenuLinkTreeInterface $menuTree;

  /**
   * Constructs a SystemManager object.
   *
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_tree
   *   The menu tree manager.
   * @param \Drupal\Core\Menu\MenuActiveTrailInterface $menu_active_trail
   *   The active menu trail service.
   */
  public function __construct(
    MenuLinkTreeInterface $menu_tree,
    MenuActiveTrailInterface $menu_active_trail,
  ) {
    $this->menuTree = $menu_tree;
    $this->menuActiveTrail = $menu_active_trail;
  }

  /**
   * Build dashboard.
   */
  public function buildIndex() {
    $link = $this->menuActiveTrail->getActiveLink('app');
    if ($link && $content = $this->getAdminBlock($link)) {
      $build = [
        '#theme' => 'admin_block_content',
        '#content' => $content,
      ];
    }
    else {
      $build = [
        '#markup' => 'You do not have any administrative items.',
      ];
    }
    return $build;
  }

  /**
   * Provide a single block on the administration overview page.
   *
   * @param \Drupal\Core\Menu\MenuLinkInterface $instance
   *   The menu item to be displayed.
   *
   * @return array
   *   An array of menu items, as expected by admin-block-content.html.twig.
   */
  public function getAdminBlock(MenuLinkInterface $instance) {
    $content = [];
    // Only find the children of this link.
    $link_id = $instance->getPluginId();
    $parameters = new MenuTreeParameters();
    $parameters->setRoot($link_id)->excludeRoot()->setTopLevelOnly()->onlyEnabledLinks();
    $tree = $this->menuTree->load(NULL, $parameters);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $this->menuTree->transform($tree, $manipulators);
    foreach ($tree as $key => $element) {
      // Only render accessible links.
      if (!$element->access->isAllowed()) {
        // @todo Bubble cacheability metadata of both accessible and
        //   inaccessible links. Currently made impossible by the way admin
        //   blocks are rendered.
        continue;
      }

      /** @var \Drupal\Core\Menu\MenuLinkInterface $link */
      $link = $element->link;
      $content[$key]['title'] = $link->getTitle();
      $content[$key]['options'] = $link->getOptions();
      $content[$key]['description'] = $link->getDescription();
      $content[$key]['url'] = $link->getUrlObject();
    }
    ksort($content);
    return $content;
  }

}
