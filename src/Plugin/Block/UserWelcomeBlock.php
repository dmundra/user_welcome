<?php

namespace Drupal\user_welcome\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a user welcome block.
 *
 * @Block(
 *   id = "user_welcome_block",
 *   admin_label = @Translation("User Welcome"),
 *   category = @Translation("Custom"),
 * )
 */
class UserWelcomeBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => $this->t('Hello <username>!<br/>Your last log in was <login date>.</br><a href="/user">Visit your profile</a>!welcome_message', [
        '!welcome_message' => '<br/>' . $config['welcome_message'] ?? '',
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
  
    $config = $this->getConfiguration();

    $form['welcome_message'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Welcome Message'),
      '#description' => $this->t('Message to display to users who have logged in.'),
      '#format' => 'plain_text',
      '#default_value' => $config['welcome_message'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['user_welcome_block_settings'] = $form_state->getValue('user_welcome_block_settings');
    $this->configuration['welcome_message'] = $form_state->getValue('welcome_message');
  }

}
