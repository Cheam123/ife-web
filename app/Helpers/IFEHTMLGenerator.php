<?php

/**
 * Modern IFE Report HTML Generator
 * 
 * Enhanced with modern design principles, accessibility, and maintainability
 * Follows SOLID principles and clean code practices
 * 
 * 
 */

namespace App\Helpers;

use App\Models\IfeArea;
class IFEHTMLGenerator
{
  private const COLORS = [
    'primary' => '#007AFF',
    'primaryLight' => '#E8F4FD',
    'success' => '#34C759',
    'successLight' => '#E8F8EA',
    'warning' => '#FF9500',
    'warningLight' => '#FFF4E6',
    'error' => '#FF3B30',
    'errorLight' => '#FFEBEA',
    'neutral100' => '#FFFFFF',
    'neutral200' => '#F8F9FA',
    'neutral300' => '#E9ECEF',
    'neutral400' => '#DEE2E6',
    'neutral500' => '#ADB5BD',
    'neutral600' => '#6C757D',
    'neutral700' => '#495057',
    'neutral800' => '#343A40',
    'neutral900' => '#212529',
    'textPrimary' => '#1C1C1E',
    'textSecondary' => '#8E8E93',
    'textTertiary' => '#C7C7CC',
    'textInverse' => '#FFFFFF',
    'surfacePrimary' => '#FFFFFF',
    'surfaceSecondary' => '#F2F2F7',
    'surfaceTertiary' => '#E5E5EA'
  ];

  private const TYPOGRAPHY = [
    'fontFamily' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
    'baseFontSize' => '16px',
    'baseLineHeight' => '1.5',
    'headingFontWeight' => '600',
    'bodyFontWeight' => '400',
    'labelFontWeight' => '600'
  ];

  private const SPACING = [
    'xs' => '4px',
    'sm' => '8px',
    'md' => '16px',
    'lg' => '24px',
    'xl' => '32px'
  ];

  private const BORDER_RADIUS = [
    'sm' => '8px',
    'md' => '12px',
    'lg' => '16px',
    'pill' => '9999px'
  ];

  /**
   * Generate modern IFE report HTML with enhanced UX
   */
  public function generateIfeReportHTML($ifeReport, array $options = []): string
  {
    $options = array_merge([
      'includeFiles' => true,
      'includeTaskInfo' => true,
      'theme' => 'light',
      'showEmptyFields' => false
    ], $options);

    return $this->buildHtmlDocument($ifeReport, $options);
  }

  private function buildHtmlDocument($ifeReport, array $options): string
  {
    $sections = [
      $this->buildReportSummarySection($ifeReport),
      // $this->buildTaskSection($ifeReport, $options),
      $this->buildCompanySection($ifeReport, $options),
      $this->buildLocationSection($ifeReport, $options),
      $this->buildTechnicalSection($ifeReport, $options),
      $this->buildContactSection($ifeReport, $options),
      $this->buildFollowupSection($ifeReport)
    ];

    $content = implode("\n", array_filter($sections));

    return $this->wrapInContainer($content);
  }

  private function wrapInContainer(string $content): string
  {
    $containerStyles = $this->buildStyles([
      'padding' => self::SPACING['xs'],
      'font-family' => self::TYPOGRAPHY['fontFamily'],
      'font-size' => self::TYPOGRAPHY['baseFontSize'],
      'color' => self::COLORS['textPrimary'],
      'line-height' => self::TYPOGRAPHY['baseLineHeight'],
      'background-color' => self::COLORS['neutral200'],
      'min-height' => '100vh'
    ]);

    return "<div style=\"{$containerStyles}\">{$content}</div>";
  }

  private function buildReportSummarySection($ifeReport): string
  {
    $rows = [
      $this->buildInfoRow('REPORT ID', "#{$ifeReport->id}", 'id'),
      $this->buildInfoRow('CREATED', $ifeReport->created_at->format('M j, Y g:i A'), 'date'),
      $this->buildInfoRow('LAST UPDATED', $ifeReport->updated_at->format('M j, Y g:i A'), 'date', true)
    ];

    return $this->buildSection(
      '📊 Report Summary',
      implode('', $rows),
      'primary'
    );
  }

  private function buildTaskSection($ifeReport, array $options): string
  {
    if (!$ifeReport->task_id) {
      return '';
    }

    $rows = [
      $this->buildInfoRow('TASK ID', "#{$ifeReport->task_id}", 'id')
    ];

    if ($options['includeTaskInfo'] && $ifeReport->task) {
      $rows[] = $this->buildInfoRow(
        'TASK TITLE',
        htmlspecialchars($ifeReport->task->title ?? 'N/A'),
        'text'
      );

      if ($ifeReport->task->description) {
        $rows[] = $this->buildDescriptionBlock(
          'DESCRIPTION',
          nl2br(htmlspecialchars($ifeReport->task->description))
        );
      }
    }

    return $this->buildSection(
      '📋 Task Information',
      implode('', $rows),
      'info'
    );
  }

  private function buildCompanySection($ifeReport, array $options): string
  {
    $rows = [];

    // Only show non-empty fields or if showEmptyFields is true
    if ($ifeReport->company_name || $options['showEmptyFields']) {
      $value = $ifeReport->company_name
        ? htmlspecialchars($ifeReport->company_name)
        : $this->buildEmptyValue('Not specified');
      $rows[] = $this->buildInfoRow('COMPANY NAME', $value, 'text');
    }

    $rows[] = $this->buildInfoRow('SHOP NAME', htmlspecialchars($ifeReport->shop_name), 'text');
    $rows[] = $this->buildInfoRow('BUSINESS TYPE', htmlspecialchars($ifeReport->nature_of_business), 'text');
    $rows[] = $this->buildInfoRow('STATUS', $this->buildStatusBadge($ifeReport->status), 'badge', true);

    return $this->buildSection(
      '🏢 Company Information',
      implode('', $rows),
      'company'
    );
  }

  private function buildLocationSection($ifeReport, array $options): string
  {
    $ifeAreaName = IfeArea::find($ifeReport->ife_area)?->area ?? 'Unknown Area';

    $rows = [
      $this->buildInfoRow('IFE AREA', htmlspecialchars($ifeAreaName), 'text'),
      $this->buildInfoRow('LOCATION', htmlspecialchars($ifeReport->location), 'text'),
      // $this->buildDescriptionBlock('LOCATION', htmlspecialchars($ifeReport->location), true)
    ];

    return $this->buildSection(
      '📍 Location & Area',
      implode('', $rows),
      'location'
    );
  }

  private function buildTechnicalSection($ifeReport, array $options): string
  {
    $rows = [];

    if ($ifeReport->problem_description) {
      $rows[] = $this->buildDescriptionBlock(
        'PROBLEM',
        nl2br(htmlspecialchars($ifeReport->problem_description))
      );
    }

    $rows[] = $this->buildInfoRow('SUPPORT TYPE', htmlspecialchars($ifeReport->support_required), 'text');

    if ($ifeReport->support_description) {
      $rows[] = $this->buildDescriptionBlock(
        'SUPPORT DETAILS',
        nl2br(htmlspecialchars($ifeReport->support_description))
      );
    }

    if ($ifeReport->personal_remarks) {
      $rows[] = $this->buildDescriptionBlock(
        'REMARKS',
        nl2br(htmlspecialchars($ifeReport->personal_remarks)),
        true
      );
    }

    return $this->buildSection(
      '🔧 Technical Details',
      implode('', $rows),
      'technical'
    );
  }

  private function buildContactSection($ifeReport, array $options): string
  {
    $rows = [
      $this->buildInfoRow('PIC NAME', htmlspecialchars($ifeReport->pic_name), 'text')
    ];

    if ($ifeReport->mobile_number) {
      $rows[] = $this->buildInfoRow(
        'MOBILE',
        $this->buildContactLink($ifeReport->mobile_number, 'tel'),
        'contact'
      );
    }

    if ($ifeReport->other_mobile_numbers) {
      $rows[] = $this->buildInfoRow(
        '📞 OTHER',
        htmlspecialchars($ifeReport->other_mobile_numbers),
        'text'
      );
    }

    if ($ifeReport->email) {
      $rows[] = $this->buildInfoRow(
        '✉️ EMAIL',
        $this->buildContactLink($ifeReport->email, 'mailto'),
        'contact',
        true
      );
    }

    return $this->buildSection(
      '👤 Contact Information',
      implode('', $rows),
      'contact'
    );
  }

  private function buildFollowupSection($ifeReport): string
  {
    $followupDate = date('M j, Y', strtotime($ifeReport->next_followup_date));
    $daysUntil = (int) ceil((strtotime($ifeReport->next_followup_date) - time()) / (60 * 60 * 24));

    $urgencyClass = $this->getFollowupUrgencyClass($daysUntil);
    $urgencyColor = $this->getFollowupUrgencyColor($urgencyClass);

    $contentStyles = $this->buildStyles([
      'padding' => self::SPACING['md'],
      'text-align' => 'center',
      'background' => "linear-gradient(135deg, {$urgencyColor}, " . $this->adjustColorBrightness($urgencyColor, -20) . ")",
      'color' => self::COLORS['textInverse'],
      'border-radius' => self::BORDER_RADIUS['md'],
      'position' => 'relative',
      'overflow' => 'hidden'
    ]);

    $dateStyles = $this->buildStyles([
      'font-size' => '24px',
      'font-weight' => 'bold',
      'margin-bottom' => self::SPACING['sm']
    ]);

    $urgencyStyles = $this->buildStyles([
      'font-size' => '14px',
      'opacity' => '0.9',
      'text-transform' => 'uppercase',
      'letter-spacing' => '1px',
      'font-weight' => '600'
    ]);

    $urgencyText = $this->getUrgencyText($daysUntil);

    $content = "
            <div style=\"{$contentStyles}\">
                <div style=\"{$dateStyles}\">{$followupDate}</div>
                <div style=\"{$urgencyStyles}\">{$urgencyText}</div>
            </div>
        ";

    return $this->buildSection(
      '📅 Next Follow-up',
      $content,
      'followup',
      false
    );
  }

  private function buildSection(string $title, string $content, string $type, bool $hasPadding = true): string
  {
    $sectionStyles = $this->buildStyles([
      'background-color' => self::COLORS['surfacePrimary'],
      'border-radius' => self::BORDER_RADIUS['sm'],
      'margin-bottom' => self::SPACING['md'],
      'box-shadow' => '0 2px 8px rgba(0, 0, 0, 0.08)',
      'overflow' => 'hidden'
    ]);

    $headerStyles = $this->buildStyles([
      'background-color' => self::COLORS['surfaceSecondary'],
      'padding' => self::SPACING['xs'] . ' ' . self::SPACING['xs'],
      'border-bottom' => '1px solid ' . self::COLORS['surfaceTertiary']
    ]);

    $titleStyles = $this->buildStyles([
      'color' => self::COLORS['textPrimary'],
      'font-size' => '18px',
      'font-weight' => self::TYPOGRAPHY['headingFontWeight'],
      'margin' => '0',
      'display' => 'flex',
      'align-items' => 'center',
      'gap' => self::SPACING['sm']
    ]);

    $contentWrapperStyles = $hasPadding ? $this->buildStyles([
      'padding' => self::SPACING['sm']
    ]) : '';

    return "
            <div style=\"{$sectionStyles}\">
                <div style=\"{$headerStyles}\">
                    <h3 style=\"{$titleStyles}\">{$title}</h3>
                </div>
                <div style=\"{$contentWrapperStyles}\">{$content}</div>
            </div>
        ";
  }

  private function buildInfoRow(string $label, string $value, string $type, bool $isLast = false): string
  {
    $rowStyles = $this->buildStyles([
      'display' => 'flex',
      'flex-direction' => 'column',
      'justify-content' => 'space-between',
      'align-items' => 'center',
      'padding' => self::SPACING['xs'] . ' 0',
      'min-height' => '48px'
    ]);

    // if (!$isLast) {
    //   $rowStyles .= '; border-bottom: 1px solid ' . self::COLORS['surfaceTertiary'];
    // }

    $labelStyles = $this->buildStyles([
      'color' => self::COLORS['textSecondary'],
      'font-weight' => self::TYPOGRAPHY['labelFontWeight'],
      'font-size' => '13px',
      'text-transform' => 'uppercase',
      'letter-spacing' => '0.5px',
      'flex-shrink' => '0'
    ]);

    $valueStyles = $this->buildStyles([
      'color' => self::COLORS['textPrimary'],
      'font-weight' => self::TYPOGRAPHY['bodyFontWeight'],
      'font-size' => '14px',
      'text-transform' => 'uppercase',
      'letter-spacing' => '0.5px',
      'flex-shrink' => '0',
      'margin-left' => self::SPACING['sm']
    ]);

    // Adjust value styles based on type
    if ($type === 'contact') {
      $valueStyles .= '; color: ' . self::COLORS['primary'];
    }

    return "
            <div style=\"{$rowStyles}\">
                <span style=\"{$labelStyles}\">{$label}</span>
            </div>
            <div style=\"{$rowStyles}\">
                <span style=\"{$valueStyles}\">{$value}</span>
            </div>

        ";
  }

  private function buildDescriptionBlock(string $label, string $content, bool $isLast = false): string
  {
    $blockStyles = $this->buildStyles([
      'padding' => self::SPACING['md'] . ' 0'
    ]);

    if (!$isLast) {
      $blockStyles .= '; border-bottom: 1px solid ' . self::COLORS['surfaceTertiary'];
    }

    $labelStyles = $this->buildStyles([
      'color' => self::COLORS['textSecondary'],
      'font-weight' => self::TYPOGRAPHY['labelFontWeight'],
      'font-size' => '13px',
      'text-transform' => 'uppercase',
      'letter-spacing' => '0.5px',
      'margin-bottom' => self::SPACING['sm'],
      'display' => 'block'
    ]);

    $contentStyles = $this->buildStyles([
      'color' => self::COLORS['textPrimary'],
      'line-height' => '1.6',
      'font-size' => '16px'
    ]);

    return "
            <div style=\"{$blockStyles}\">
                <div style=\"{$labelStyles}\">{$label}</div>
                <div style=\"{$contentStyles}\">{$content}</div>
            </div>
        ";
  }

  private function buildStatusBadge(string $status): string
  {
    $normalizedStatus = strtolower(trim($status));
    $colors = $this->getStatusColors($normalizedStatus);

    $badgeStyles = $this->buildStyles([
      'background-color' => $colors['background'],
      'color' => $colors['text'],
      'padding' => '8px 16px',
      'border-radius' => self::BORDER_RADIUS['pill'],
      'font-size' => '12px',
      'font-weight' => '600',
      'text-transform' => 'uppercase',
      'letter-spacing' => '0.5px',
      'display' => 'inline-block',
      'min-height' => '32px',
      'line-height' => '16px'
    ]);

    return "<span style=\"{$badgeStyles}\">" . htmlspecialchars($status) . "</span>";
  }

  private function buildContactLink(string $contact, string $type): string
  {
    $linkStyles = $this->buildStyles([
      'color' => self::COLORS['primary'],
      'font-weight' => '600',
      'text-decoration' => 'none'
    ]);

    $href = $type === 'tel' ? "tel:{$contact}" : "mailto:{$contact}";

    return "<a href=\"{$href}\" style=\"{$linkStyles}\">" . htmlspecialchars($contact) . "</a>";
  }

  private function buildEmptyValue(string $text): string
  {
    $styles = $this->buildStyles([
      'color' => self::COLORS['textSecondary'],
      'font-style' => 'italic',
      'opacity' => '0.8'
    ]);

    return "<em style=\"{$styles}\">{$text}</em>";
  }

  private function buildStyles(array $styles): string
  {
    return implode('; ', array_map(
      fn($key, $value) => "{$key}: {$value}",
      array_keys($styles),
      array_values($styles)
    ));
  }

  private function getStatusColors(string $status): array
  {
    $statusColorMap = [
      'active' => ['background' => self::COLORS['successLight'], 'text' => self::COLORS['success']],
      'completed' => ['background' => self::COLORS['successLight'], 'text' => self::COLORS['success']],
      'pending' => ['background' => self::COLORS['warningLight'], 'text' => self::COLORS['warning']],
      'waiting' => ['background' => self::COLORS['warningLight'], 'text' => self::COLORS['warning']],
      'cancelled' => ['background' => self::COLORS['errorLight'], 'text' => self::COLORS['error']],
      'failed' => ['background' => self::COLORS['errorLight'], 'text' => self::COLORS['error']]
    ];

    return $statusColorMap[$status] ?? [
      'background' => self::COLORS['primaryLight'],
      'text' => self::COLORS['primary']
    ];
  }

  private function getFollowupUrgencyClass(int $daysUntil): string
  {
    if ($daysUntil < 0)
      return 'overdue';
    if ($daysUntil <= 1)
      return 'urgent';
    if ($daysUntil <= 3)
      return 'soon';
    if ($daysUntil <= 7)
      return 'upcoming';
    return 'future';
  }

  private function getFollowupUrgencyColor(string $urgencyClass): string
  {
    $urgencyColors = [
      'overdue' => self::COLORS['error'],
      'urgent' => '#FF6B35',
      'soon' => self::COLORS['warning'],
      'upcoming' => '#4DABF7',
      'future' => self::COLORS['primary']
    ];

    return $urgencyColors[$urgencyClass];
  }

  private function getUrgencyText(int $daysUntil): string
  {
    if ($daysUntil < 0)
      return 'Overdue by ' . abs($daysUntil) . ' day' . (abs($daysUntil) > 1 ? 's' : '');
    if ($daysUntil === 0)
      return 'Due Today';
    if ($daysUntil === 1)
      return 'Due Tomorrow';
    if ($daysUntil <= 7)
      return "Due in {$daysUntil} days";
    return "Due in {$daysUntil} days";
  }

  private function adjustColorBrightness(string $hexColor, int $percent): string
  {
    // Remove # if present
    $hex = ltrim($hexColor, '#');

    // Convert to RGB
    $rgb = array_map('hexdec', str_split($hex, 2));

    // Adjust brightness
    foreach ($rgb as &$color) {
      $color = max(0, min(255, $color + ($color * $percent / 100)));
    }

    // Convert back to hex
    return '#' . implode('', array_map(fn($c) => str_pad(dechex((int) $c), 2, '0', STR_PAD_LEFT), $rgb));
  }
}

// Usage example:
/*
$generator = new IfeReportHtmlGenerator();
$html = $generator->generateIfeReportHTML($ifeReport, [
    'includeFiles' => true,
    'includeTaskInfo' => true,
    'showEmptyFields' => false,
    'theme' => 'light'
]);
*/