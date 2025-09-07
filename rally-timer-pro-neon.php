<?php
/**
 * Plugin Name: Rally Timer Pro (Neon)
 * Description: Full-screen rally coordinator (Vanilla JS). Add players (distance in seconds), set rally duration (minutes), per-player countdowns. Now with Edit, Clear All, robust click handlers for mobile/Elementor.
 * Version: 1.3.3
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

// Shortcode
add_shortcode('rally_timer', function(){
    ob_start(); ?>
    <section class="rtp-hero" data-rtp="1">
        <div class="rtp-hero-bg"></div>
        <div class="rtp-container">
            <h1 class="rtp-title">Rally Timer</h1>
            <p class="rtp-subtitle">
                Add players with their distance to the castle (in seconds). Set rally duration (minutes).
                When you <strong>Start</strong>, each player gets a countdown for when to begin rallying so everyone arrives together.
                <span class="rtp-chip" id="rtp-chip">script ready ✓</span>
            </p>

            <div class="rtp-card" id="rtp-card">
                <div class="rtp-grid">
                    <div class="rtp-field">
                        <label>Rally Duration (minutes)</label>
                        <input type="number" id="rtp-duration-min" value="5" min="1" step="1">
                    </div>

                    <div class="rtp-field">
                        <label>Player Name</label>
                        <input type="text" id="rtp-name" placeholder="Player name">
                    </div>

                    <div class="rtp-field">
                        <label>Distance to Castle (seconds)</label>
                        <input type="number" id="rtp-distance" placeholder="e.g., 25" min="0" step="1">
                    </div>

                    <div class="rtp-actions">
                        <button type="button" class="rtp-btn rtp-primary" id="rtp-add" data-action="add">Add Player</button>
                        <button type="button" class="rtp-btn" id="rtp-reset" data-action="reset">Reset</button>
                        <button type="button" class="rtp-btn rtp-danger" id="rtp-clear" data-action="clear">Clear All</button>
                    </div>
                </div>

                <div class="rtp-table-wrap">
                    <table class="rtp-table" id="rtp-table" aria-live="polite">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Distance (s)</th>
                                <th>Starts In</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="rtp-run">
                    <button type="button" class="rtp-btn rtp-primary rtp-start" id="rtp-start" data-action="start" disabled>Start</button>

                    <div class="rtp-stats">
                        <div class="rtp-stat">
                            <div class="rtp-stat-label">Next To Start</div>
                            <div class="rtp-stat-value" id="rtp-next-name">—</div>
                            <div class="rtp-stat-small" id="rtp-next-in">—</div>
                        </div>
                        <div class="rtp-stat">
                            <div class="rtp-stat-label">Group Arrival In</div>
                            <div class="rtp-timer-big" id="rtp-arrival">--:--</div>
                        </div>
                    </div>
                </div>
                <div class="rtp-toast" id="rtp-toast" aria-live="polite" style="display:none;"></div>
            </div>

            <p class="rtp-footnote">Tip: Farthest player starts first. Everyone else starts later so all arrive at the same second.</p>
        </div>
    </section>
    <?php
    return ob_get_clean();
});

// Assets in header to avoid themes without wp_footer
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('rtp-css', plugin_dir_url(__FILE__) . 'assets/rtp.css', [], '1.3.3');
    wp_enqueue_script('rtp-js', plugin_dir_url(__FILE__) . 'assets/rtp.vanilla.js', [], '1.3.3', false /* in header */);
});
