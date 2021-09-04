<?php

namespace Drupal\user_welcome\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\user\Entity\User;

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
    $config = $this->getConfiguration();

    $user = User::load(\Drupal::currentUser()->id());

    // Format example: December 21st, 2012 9:01 am.
    $formatted_date = \Drupal::service('date.formatter')->format($user->getLastLoginTime(), 'custom', 'F jS, Y g:i a');
    $url = Url::fromRoute('entity.user.canonical', ['user' => $user->id()]);

    return [
      '#markup' => $this->t('Hello :username!<br/>Your last log in was %date.</br><a href="@url">Visit your profile</a><br/>:welcome', [
        ':username' => $user->getUsername(),
        '%date' => $formatted_date,
        '@url' => $url->toString(),
        ':welcome' => $config['welcome_message']['value'] ?? '',
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIf(!\Drupal::currentUser()->isAnonymous());
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
      '#allowed_formats' => ['plain_text'],
      '#default_value' => $this->configuration['welcome_message']['value'] ?? '',
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

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
