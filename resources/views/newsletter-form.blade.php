@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script src="/build/assets/tinymce/tinymce.min.js"></script>

<script>
    let isCodeModeActive = false;
    let selectedTemplateKey = 'clean_letter';

    // Clean, Badge-Free WYSIWYG Editor Initialization
    tinymce.init({
        selector: 'textarea#body_template',
        license_key: 'gpl',
        suffix: '.min',
        height: 520,
        menubar: false,
        branding: false,
        promotion: false,
        statusbar: false,
        plugins: 'table lists link image code visualblocks fullscreen',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | align lineheight | bullist numlist | table link image | code removeformat fullscreen',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 1.6; padding: 15px; background: #ffffff; color: #0f172a; }',
        setup: function(editor) {
            editor.on('change keyup paste', function() {
                const content = editor.getContent();
                const codeArea = document.getElementById('rawHtmlCodeEditor');
                if (codeArea) codeArea.value = content;
            });
        }
    });

    // Global WYSIWYG Helpers
    window.getEditorContent = function() {
        if (tinymce.activeEditor) {
            return tinymce.activeEditor.getContent();
        }
        return document.getElementById('body_template')?.value || '';
    };

    window.setEditorContent = function(html) {
        if (tinymce.activeEditor) {
            tinymce.activeEditor.setContent(html);
        }
        const txtArea = document.getElementById('body_template');
        if (txtArea) txtArea.value = html;
        const codeArea = document.getElementById('rawHtmlCodeEditor');
        if (codeArea) codeArea.value = html;
    };

    window.insertContentToEditor = function(snippet) {
        if (tinymce.activeEditor && !isCodeModeActive) {
            tinymce.activeEditor.insertContent(snippet);
        } else {
            const codeArea = document.getElementById('rawHtmlCodeEditor');
            if (codeArea) codeArea.value += '\n' + snippet;
        }
    };

    // Comprehensive, Highly-Editable Modern Email Templates
    const EMAIL_TEMPLATES = {
        // --- 1. GENERIC & CLEAN ---
        'clean_letter': {
            name: 'Clean Minimal Letter (Plain & Simple)',
            category: 'generic',
            categoryLabel: 'Generic & Clean',
            icon: 'fa-envelope-open',
            desc: 'Clean human-to-human email. Highest inbox delivery and response rate.',
            subject: 'Quick note regarding @{{ company | your team }}',
            body: `<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">Hi @{{ firstname | there }},</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">I hope your week is off to a great start.</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">I'm reaching out because we've recently helped teams similar to <strong>@{{ company | your company }}</strong> streamline their workflow and achieve measurable improvements.</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">Would you be open to a quick 5-minute conversation this week to see if this makes sense for your team?</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">Best regards,</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; color: #475569; line-height: 1.5; margin-top: 15px;">
    <strong style="color: #0f172a;">{{ Auth::user()->name ?? 'Your Name' }}</strong><br>
    Founder / Growth Lead<br>
    <a href="https://example.com" style="color: #f59e0b; text-decoration: none;">yourcompany.com</a>
</p>`
        },

        'quick_followup': {
            name: 'Polite Follow-up & Touchpoint',
            category: 'generic',
            categoryLabel: 'Generic & Clean',
            icon: 'fa-reply',
            desc: 'Short, non-pushy follow-up message to revive previous conversations.',
            subject: 'Following up on our note — @{{ firstname | there }}',
            body: `<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">Hi @{{ firstname | there }},</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">I know things get busy, so I just wanted to gently follow up on my previous note.</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">Is improving deliverability at <strong>@{{ company | your team }}</strong> currently on your radar for this quarter?</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">If yes, feel free to reply with a convenient time to connect, or let me know if I should follow up next month instead.</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; color: #64748b; line-height: 1.5;">Cheers,<br><strong style="color: #0f172a;">{{ Auth::user()->name ?? 'Your Name' }}</strong></p>`
        },

        'general_announcement': {
            name: 'Standard Notice & Announcement',
            category: 'generic',
            categoryLabel: 'Generic & Clean',
            icon: 'fa-bullhorn',
            desc: 'Universal update format with clean header, summary callout, and action button.',
            subject: 'Important Update: New changes for @{{ company | all users }}',
            body: `<div style="max-width: 600px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1e293b;">
    <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0;">Important Platform Update</h2>
    <p style="font-size: 15px; line-height: 1.6; color: #334155;">Hello @{{ firstname | Valued Partner }},</p>
    <p style="font-size: 15px; line-height: 1.6; color: #334155;">We are writing to share an important milestone and upcoming enhancement that directly benefits <strong>@{{ company | your account }}</strong>.</p>
    
    <div style="background-color: #f8fafc; border-left: 4px solid #0f172a; padding: 16px 20px; border-radius: 6px; margin: 20px 0;">
        <h4 style="margin: 0 0 6px 0; color: #0f172a; font-size: 15px;">Key Summary Points:</h4>
        <ul style="margin: 0; padding-left: 18px; font-size: 14px; color: #475569; line-height: 1.6;">
            <li><strong>Zero Downtime Upgrade:</strong> All services remain 100% operational.</li>
            <li><strong>Faster Speeds:</strong> 3x improvement in mail queue processing rates.</li>
            <li><strong>Granular Control:</strong> Live pre-send audience editing is now active.</li>
        </ul>
    </div>

    <p style="font-size: 15px; line-height: 1.6; color: #334155;">If you have any questions, our support team is available 24/7 to assist you.</p>
    
    <div style="margin: 25px 0;">
        <a href="https://example.com/learn-more" style="background-color: #0f172a; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">Read Full Release Notes &rarr;</a>
    </div>

    <p style="font-size: 14px; color: #64748b; line-height: 1.5; border-top: 1px solid #e2e8f0; padding-top: 15px;">
        Best,<br><strong style="color: #0f172a;">The Operations Team</strong>
    </p>
</div>`
        },

        'promo_card': {
            name: 'Simple Card Offer & Promo',
            category: 'generic',
            categoryLabel: 'Generic & Clean',
            icon: 'fa-tags',
            desc: 'Boxed card with centered offer badge, highlighted benefits, and bold button.',
            subject: 'Special Offer for @{{ company | your team }} this month',
            body: `<div style="max-width: 560px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; text-align: center;">
    <span style="background-color: #fef3c7; color: #92400e; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 15px;">Exclusive Access</span>
    <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;">Upgrade Your Outreach Pipeline</h2>
    <p style="font-size: 15px; color: #64748b; margin: 0 0 20px 0; line-height: 1.5;">Hi @{{ firstname | there }}, claim complimentary setup support for @{{ company | your organization }}.</p>
    
    <div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 15px; margin: 20px 0;">
        <div style="font-size: 13px; color: #475569;">Use Code: <strong style="color: #0f172a; font-family: monospace; font-size: 16px;">GROWTH2026</strong></div>
    </div>

    <div style="margin: 25px 0;">
        <a href="https://example.com/redeem" style="background-color: #f59e0b; color: #0f172a; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: 800; font-size: 15px; display: inline-block;">Claim Your Access Now &rarr;</a>
    </div>

    <p style="font-size: 12px; color: #94a3b8; margin: 0;">Offer valid until end of the month for @{{ email }}.</p>
</div>`
        },

        // --- 2. COLD OUTREACH & SALES ---
        'b2b_outreach': {
            name: 'Founder B2B Cold Outreach',
            category: 'sales',
            categoryLabel: 'Cold Outreach & Sales',
            icon: 'fa-user-tie',
            desc: 'Personalized 1-on-1 sales pitch with 3 value points and a 10-min meeting link.',
            subject: 'Quick question regarding @{{ company | your team }}\'s outreach workflow',
            body: `<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">Hello @{{ firstname | there }},</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">I noticed the impressive growth at <strong>@{{ company | your company }}</strong> and wanted to reach out directly.</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">Most fast-scaling engineering and product teams struggle with fragmented cold outreach deliverability and missing real-time SMTP telemetry. We built <strong>Campaign Stack</strong> to solve exactly that:</p>
<ul style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; color: #334155; line-height: 1.8; margin-left: 20px;">
    <li><strong>Zero-fail SMTP Relay Routing:</strong> Automated fallback across multiple gateway accounts.</li>
    <li><strong>Dynamic Merge Personalization:</strong> Ultra-granular personalization per contact segment.</li>
    <li><strong>Full Pre-Send Control:</strong> Visual 4-stage pipeline with live transmission logs.</li>
</ul>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">Do you have 10 minutes this Thursday for a brief walkthrough?</p>
<div style="margin: 25px 0;">
    <a href="https://calendly.com" style="background-color: #0f172a; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">Schedule 10-Min Walkthrough &rarr;</a>
</div>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; color: #64748b; line-height: 1.6;">Best regards,<br><strong style="color: #0f172a;">{{ Auth::user()->name ?? 'Your Name' }}</strong><br>Founder &amp; Head of Product</p>`
        },

        'case_study_roi': {
            name: 'ROI Proof & Case Study Pitch',
            category: 'sales',
            categoryLabel: 'Cold Outreach & Sales',
            icon: 'fa-chart-line',
            desc: 'Client testimonial quote box with concrete deliverability metrics and link.',
            subject: 'How Razorpay boosted B2B inbox placement to 99.4% with Campaign Stack',
            body: `<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">Hello @{{ firstname | there }},</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">When sales teams at enterprise fintech firms scale outreach, over 28% of emails get trapped in spam filters due to single-server bottlenecks.</p>

<div style="background-color: #f1f5f9; padding: 18px 22px; border-radius: 10px; margin: 20px 0; border: 1px solid #e2e8f0;">
    <p style="font-size: 14px; color: #0f172a; font-style: italic; margin: 0 0 10px 0; line-height: 1.5;">
        &ldquo;Campaign Stack's dynamic multi-gateway failover allowed us to achieve 99.4% inbox placement without risking our primary domain reputation.&rdquo;
    </p>
    <div style="font-size: 12px; font-weight: bold; color: #475569;">— Vikram Aditya, Growth Operations</div>
</div>

<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">We can implement the same deliverability architecture for <strong>@{{ company | your team }}</strong> in under 15 minutes.</p>

<div style="margin: 25px 0;">
    <a href="https://calendly.com" style="background-color: #059669; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">Read Full 2-Page Case Study &rarr;</a>
</div>

<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; color: #64748b;">Best,<br><strong style="color: #0f172a;">{{ Auth::user()->name ?? 'Your Name' }}</strong><br>Deliverability Specialist</p>`
        },

        // --- 3. NEWSLETTERS & CONTENT ---
        'weekly_digest': {
            name: 'Editorial Newsletter & Weekly Digest',
            category: 'content',
            categoryLabel: 'Newsletters & Digest',
            icon: 'fa-newspaper',
            desc: '3-story clean editorial newsletter layout with issue badge and divider lines.',
            subject: 'The Growth Dispatch #42: Modern Cold Email Architecture for @{{ company | Founders }}',
            body: `<div style="max-width: 600px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1e293b;">
    <div style="border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 25px;">
        <h2 style="margin: 0; font-size: 20px; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">The Growth Dispatch</h2>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Issue #42 &bull; Prepared exclusively for @{{ firstname | Subscriber }}</div>
    </div>

    <h3 style="font-size: 17px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">1. The Death of Cold Email Mass Blasts</h3>
    <p style="font-size: 14px; color: #334155; line-height: 1.6; margin: 0 0 15px 0;">
        Major inbox providers now enforce strict SPF, DKIM, and DMARC alignments. High-volume single-IP senders face immediate throttling.
    </p>

    <h3 style="font-size: 17px; font-weight: 800; color: #0f172a; margin: 25px 0 8px 0;">2. Personalized Merge Tags with Safe Fallbacks</h3>
    <p style="font-size: 14px; color: #334155; line-height: 1.6; margin: 0 0 20px 0;">
        Never send &ldquo;Hello &lt;First Name&gt;&rdquo; again. Using fallback pipelines like <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">@{{ firstname | there }}</code> ensures natural readability every time.
    </p>

    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0;">
        <h4 style="margin: 0 0 8px 0; color: #0f172a; font-size: 15px;">Enjoying this weekly briefing?</h4>
        <p style="margin: 0 0 15px 0; font-size: 13px; color: #64748b;">Forward to a colleague at @{{ company | your team }} who manages outbound sales.</p>
        <a href="https://example.com" style="color: #f59e0b; font-weight: bold; text-decoration: none; font-size: 13px;">View All Previous Issues &rarr;</a>
    </div>
</div>`
        },

        'product_announcement': {
            name: 'Product Launch & Feature Showcase',
            category: 'content',
            categoryLabel: 'Newsletters & Digest',
            icon: 'fa-rocket',
            desc: 'Hero typography, feature pill badges, 2-column benefit grid, and "Try Now" button.',
            subject: 'Introducing Campaign Stack 2.0: Automated Dispatch Studio 🚀',
            body: `<div style="max-width: 600px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1e293b;">
    <div style="text-align: center; padding: 15px 0 25px 0;">
        <span style="background-color: #ecfdf5; color: #059669; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; border: 1px solid #a7f3d0;">✨ What's New</span>
        <h1 style="font-size: 24px; font-weight: 900; color: #0f172a; margin: 15px 0 8px 0;">Deliverability Reimagined</h1>
        <p style="font-size: 14px; color: #64748b; margin: 0;">Everything you need to send personalized cold sequences with zero CLI hassle.</p>
    </div>

    <p style="font-size: 15px; line-height: 1.6;">Hi @{{ firstname | there }},</p>
    <p style="font-size: 15px; line-height: 1.6;">We've been working on our biggest update yet for teams scaling their outreach at <strong>@{{ company | your company }}</strong>:</p>

    <div style="margin: 20px 0;">
        <div style="padding: 14px 18px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 12px;">
            <h4 style="margin: 0 0 4px 0; color: #0f172a; font-size: 14px;">🎛 4-Stage Progressive Dispatch Studio</h4>
            <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.5;">Review audience lists, toggle exclusions, and edit lead contact info right before queueing.</p>
        </div>
        <div style="padding: 14px 18px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
            <h4 style="margin: 0 0 4px 0; color: #0f172a; font-size: 14px;">⚡ Real-Time SMTP Test &amp; Live Terminal</h4>
            <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.5;">Stream transmission telemetry line-by-line with instant verification feedback.</p>
        </div>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="https://example.com/login" style="background-color: #0f172a; color: #ffffff; padding: 12px 26px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">Try The New Studio &rarr;</a>
    </div>
</div>`
        },

        // --- 4. EVENTS & FEEDBACK ---
        'executive_summit': {
            name: 'Executive Briefing & VIP Invitation',
            category: 'events',
            categoryLabel: 'Events & Invites',
            icon: 'fa-calendar-check',
            desc: 'Luxury dark gradient header, VIP badge, agenda callout box, and gold RSVP button.',
            subject: 'Exclusive Invitation: Cloud Security & Compliance Summit — @{{ firstname | Leader }}',
            body: `<div style="max-width: 600px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1e293b;">
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 25px; border-radius: 12px; color: #ffffff; text-align: center; margin-bottom: 20px;">
        <span style="background-color: rgba(245, 158, 11, 0.2); color: #fbbf24; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 10px;">Executive Pass</span>
        <h2 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 800; color: #ffffff;">Cloud Security &amp; DPDP Summit</h2>
        <p style="margin: 0; font-size: 13px; color: #94a3b8;">Bangalore Convention Center &bull; Next Month</p>
    </div>

    <p style="font-size: 15px; line-height: 1.6;">Dear @{{ salutation | Mr./Ms. }} @{{ lastname | Colleague }},</p>
    <p style="font-size: 15px; line-height: 1.6;">As <strong>@{{ company | your enterprise }}</strong> expands its critical digital infrastructure, meeting zero-trust compliance standards is essential.</p>
    
    <div style="background-color: #f8fafc; border-left: 4px solid #f59e0b; padding: 14px 18px; border-radius: 6px; margin: 18px 0;">
        <h4 style="margin: 0 0 5px 0; color: #0f172a; font-size: 14px;">Roundtable Agenda Highlights</h4>
        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.5;">Join 40+ engineering heads &amp; CTOs for an exclusive closed-door discussion on automated data residency, AI governance, and infrastructure security.</p>
    </div>

    <div style="text-align: center; margin: 25px 0;">
        <a href="https://example.com/rsvp" style="background-color: #f59e0b; color: #0f172a; padding: 12px 26px; text-decoration: none; border-radius: 8px; font-weight: 800; font-size: 14px; display: inline-block;">Reserve Complimentary Pass &rarr;</a>
    </div>
</div>`
        },

        'quick_feedback': {
            name: 'Quick 1-Click Feedback & Survey',
            category: 'events',
            categoryLabel: 'Events & Invites',
            icon: 'fa-star',
            desc: 'Clean 1-5 rating buttons with a polite 30-second customer feedback request.',
            subject: 'How was your experience with @{{ company | our team }}, @{{ firstname | there }}?',
            body: `<div style="max-width: 540px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; text-align: center; color: #1e293b; padding: 20px 0;">
    <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">Quick 30-Second Question</h2>
    <p style="font-size: 14px; color: #64748b; line-height: 1.5; margin: 0 0 25px 0;">
        Hi @{{ firstname | there }}, how likely are you to recommend our platform to a colleague at @{{ company | your company }}?
    </p>

    <div style="margin: 20px 0; display: inline-flex; gap: 8px;">
        <a href="https://example.com/rate/1" style="display: inline-block; width: 44px; height: 44px; line-height: 44px; background-color: #f1f5f9; border-radius: 8px; text-decoration: none; font-weight: 800; color: #0f172a; font-size: 15px; border: 1px solid #cbd5e1;">1</a>
        <a href="https://example.com/rate/2" style="display: inline-block; width: 44px; height: 44px; line-height: 44px; background-color: #f1f5f9; border-radius: 8px; text-decoration: none; font-weight: 800; color: #0f172a; font-size: 15px; border: 1px solid #cbd5e1;">2</a>
        <a href="https://example.com/rate/3" style="display: inline-block; width: 44px; height: 44px; line-height: 44px; background-color: #f1f5f9; border-radius: 8px; text-decoration: none; font-weight: 800; color: #0f172a; font-size: 15px; border: 1px solid #cbd5e1;">3</a>
        <a href="https://example.com/rate/4" style="display: inline-block; width: 44px; height: 44px; line-height: 44px; background-color: #f1f5f9; border-radius: 8px; text-decoration: none; font-weight: 800; color: #0f172a; font-size: 15px; border: 1px solid #cbd5e1;">4</a>
        <a href="https://example.com/rate/5" style="display: inline-block; width: 44px; height: 44px; line-height: 44px; background-color: #f59e0b; border-radius: 8px; text-decoration: none; font-weight: 800; color: #0f172a; font-size: 15px; border: 1px solid #d97706;">5</a>
    </div>
    
    <div style="font-size: 11px; color: #94a3b8; display: flex; justify-content: space-between; max-width: 260px; margin: 5px auto 0 auto;">
        <span>1 = Not likely</span>
        <span>5 = Extremely likely</span>
    </div>

    <p style="font-size: 13px; color: #64748b; margin-top: 25px;">
        Thank you for helping us improve!<br>
        <strong style="color: #0f172a;">Customer Success Team</strong>
    </p>
</div>`
        }
    };

    function toggleEditorMode(mode) {
        const visualContainer = document.getElementById('visualEditorContainer');
        const codeContainer = document.getElementById('codeEditorContainer');
        const btnVisual = document.getElementById('btnModeVisual');
        const btnCode = document.getElementById('btnModeCode');

        if (mode === 'code') {
            isCodeModeActive = true;
            document.getElementById('rawHtmlCodeEditor').value = window.getEditorContent();
            visualContainer.classList.add('hidden');
            codeContainer.classList.remove('hidden');

            btnCode.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 shadow-2xs cursor-pointer';
            btnVisual.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 cursor-pointer';
        } else {
            isCodeModeActive = false;
            const updatedHtml = document.getElementById('rawHtmlCodeEditor').value;
            window.setEditorContent(updatedHtml);
            codeContainer.classList.add('hidden');
            visualContainer.classList.remove('hidden');

            btnVisual.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 shadow-2xs cursor-pointer';
            btnCode.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 cursor-pointer';
        }
    }

    // Load any user-saved custom templates from localStorage
    function loadSavedCustomTemplates() {
        try {
            const raw = localStorage.getItem('campaignstack_custom_templates');
            if (raw) {
                const customs = JSON.parse(raw);
                Object.keys(customs).forEach(key => {
                    EMAIL_TEMPLATES[key] = customs[key];
                });
            }
        } catch (e) {
            console.warn('Failed to load custom templates from localStorage:', e);
        }
    }
    loadSavedCustomTemplates();

    let currentStudioMode = 'library';

    function switchStudioMode(mode) {
        currentStudioMode = mode;
        const standardView = document.getElementById('standardTemplatesView');
        const aiCustomView = document.getElementById('aiCustomStudioView');

        document.querySelectorAll('.tpl-filter-btn').forEach(b => {
            b.className = 'tpl-filter-btn px-3 py-1.5 text-xs font-semibold rounded-xl text-zinc-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer';
        });

        if (mode === 'ai_custom') {
            if (standardView) standardView.classList.add('hidden');
            if (aiCustomView) aiCustomView.classList.remove('hidden');
            const aiBtn = document.getElementById('filterBtn_ai_custom');
            if (aiBtn) {
                aiBtn.className = 'tpl-filter-btn px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-zinc-950 shadow-md cursor-pointer flex items-center gap-1.5';
            }
            updateGeneratedAiPrompt();
        } else {
            if (standardView) standardView.classList.remove('hidden');
            if (aiCustomView) aiCustomView.classList.add('hidden');
            const activeBtn = document.getElementById(`filterBtn_${mode}`);
            if (activeBtn) {
                activeBtn.className = 'tpl-filter-btn px-3 py-1.5 text-xs font-bold rounded-xl bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 shadow-2xs cursor-pointer';
            }
            renderTemplateCards(mode);
        }
    }

    function openTemplateLibrary(initialMode = 'library') {
        loadSavedCustomTemplates();
        document.getElementById('templateLibraryModal').classList.remove('hidden');
        if (initialMode === 'ai_custom') {
            switchStudioMode('ai_custom');
        } else {
            switchStudioMode('all');
            selectTemplateForModalPreview('clean_letter');
        }
    }

    function closeTemplateLibrary() {
        document.getElementById('templateLibraryModal').classList.add('hidden');
    }

    function filterTemplates(cat) {
        switchStudioMode(cat);
    }

    function renderTemplateCards(filterCat) {
        const container = document.getElementById('templateCardsContainer');
        if (!container) return;
        container.innerHTML = '';

        const keys = Object.keys(EMAIL_TEMPLATES);
        let visibleCount = 0;

        keys.forEach(key => {
            const t = EMAIL_TEMPLATES[key];
            if (filterCat !== 'all' && t.category !== filterCat) return;
            visibleCount++;

            const isSelected = key === selectedTemplateKey;
            const isCustom = t.category === 'custom';

            const card = document.createElement('div');
            card.className = `p-4 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between space-y-2 ${isSelected ? 'bg-amber-50/80 border-amber-400 dark:bg-amber-950/40 dark:border-amber-500 shadow-sm' : 'bg-slate-50 dark:bg-[#09090B] border-slate-200 dark:border-zinc-800 hover:border-amber-300'}`;
            card.onclick = () => selectTemplateForModalPreview(key);

            card.innerHTML = `
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-3xs font-extrabold ${isCustom ? 'bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-200 border border-amber-300' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700'}">
                            ${t.categoryLabel}
                        </span>
                        <div class="flex items-center gap-1.5">
                            ${isCustom ? `<button type="button" onclick="event.stopPropagation(); deleteCustomTemplate('${key}');" class="text-zinc-400 hover:text-red-500 p-1 text-3xs" title="Delete custom template"><i class="fas fa-trash"></i></button>` : ''}
                            <i class="fas ${t.icon} text-xs text-amber-500"></i>
                        </div>
                    </div>
                    <h4 class="font-bold text-xs text-zinc-900 dark:text-white pt-1">${escapeHtml(t.name)}</h4>
                    <p class="text-3xs text-zinc-500 dark:text-zinc-400 leading-relaxed">${escapeHtml(t.desc)}</p>
                </div>
                <div class="pt-2 flex items-center justify-between">
                    <span class="text-3xs font-bold text-amber-600 dark:text-amber-400">Click to Preview</span>
                    <i class="fas fa-chevron-right text-3xs text-zinc-400"></i>
                </div>
            `;
            container.appendChild(card);
        });

        if (visibleCount === 0) {
            container.innerHTML = `
                <div class="p-8 text-center bg-slate-50 dark:bg-[#09090B] rounded-2xl border border-dashed border-slate-200 dark:border-zinc-800 space-y-3">
                    <i class="fas fa-magic text-2xl text-amber-500"></i>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">No templates found in this category.</p>
                    <button type="button" onclick="switchStudioMode('ai_custom')" class="px-4 py-2 bg-zinc-900 dark:bg-amber-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl shadow-2xs">
                        Create with AI Prompt &rarr;
                    </button>
                </div>
            `;
        }
    }

    function selectTemplateForModalPreview(key) {
        selectedTemplateKey = key;
        const t = EMAIL_TEMPLATES[key];
        if (!t) return;

        // Re-render cards to show active selection
        const activeFilter = currentStudioMode === 'ai_custom' ? 'all' : currentStudioMode;
        renderTemplateCards(activeFilter);

        // Update modal preview panel
        const previewTitle = document.getElementById('modalPreviewName');
        const previewSubj = document.getElementById('modalPreviewSubject');
        const previewBody = document.getElementById('modalPreviewBody');
        const btnApply = document.getElementById('btnApplySelectedTemplate');

        if (previewTitle) previewTitle.textContent = t.name;
        if (previewSubj) previewSubj.textContent = replaceMergeTags(t.subject, sampleLead);
        if (previewBody) previewBody.innerHTML = replaceMergeTags(t.body, sampleLead);
        if (btnApply) {
            btnApply.onclick = () => applyTemplate(key);
        }
    }

    function applyTemplate(key) {
        const t = EMAIL_TEMPLATES[key];
        if (!t) return;

        // Set subject
        const subj = document.getElementById('subject_template');
        if (subj) subj.value = t.subject;

        // Set editor content
        window.setEditorContent(t.body);

        closeTemplateLibrary();
    }

    // ==========================================
    // AI PROMPT GENERATOR & CUSTOM HTML LOGIC
    // ==========================================
    let aiSelectedColor = '#f59e0b';

    function setAiColor(color, btnEl) {
        aiSelectedColor = color;
        document.querySelectorAll('.ai-color-btn').forEach(b => b.classList.remove('ring-2', 'ring-amber-500', 'scale-110'));
        if (btnEl) btnEl.classList.add('ring-2', 'ring-amber-500', 'scale-110');
        const hexInput = document.getElementById('aiCustomHexColor');
        if (hexInput) hexInput.value = color;
        updateGeneratedAiPrompt();
    }

    function updateGeneratedAiPrompt() {
        const goal = document.getElementById('aiPromptGoal')?.value || 'Cold Sales Outreach & B2B Lead Generation';
        const tone = document.getElementById('aiPromptTone')?.value || 'Clean, Direct & Highly-Converting';
        const notes = document.getElementById('aiPromptNotes')?.value || '';
        const companyType = document.getElementById('aiPromptIndustry')?.value || 'SaaS / Tech / Professional Services';
        const senderName = document.getElementById('aiSenderName')?.value || '{{ Auth::user()->name ?? "Founder" }}';
        const senderTitle = document.getElementById('aiSenderTitle')?.value || 'Founder &bull; Growth Team';

        const hasHero = document.getElementById('aiSecHero')?.checked ?? true;
        const hasBullets = document.getElementById('aiSecBullets')?.checked ?? true;
        const hasQuote = document.getElementById('aiSecQuote')?.checked ?? true;
        const hasCta = document.getElementById('aiSecCta')?.checked ?? true;
        const hasFooter = document.getElementById('aiSecFooter')?.checked ?? true;

        let sectionsList = [];
        if (hasHero) sectionsList.push('- Clean Header with subtle badge & high-impact headline');
        if (hasBullets) sectionsList.push('- 2 to 3 core value propositions / bullet points with icons or emojis');
        if (hasQuote) sectionsList.push('- Styled Callout / Quote box highlighting customer proof or key metric');
        if (hasCta) sectionsList.push(`- High-contrast Call to Action (CTA) Button using brand color ${aiSelectedColor}`);
        if (hasFooter) sectionsList.push('- Clean professional sign-off and standard 1-line unsubscribe link');

        const prompt = `You are an elite Email Designer & Direct Response Copywriter.
Create a production-ready, ultra-responsive HTML email template with inline CSS tailored for maximum inbox deliverability and genuine human engagement.

============================================================
1. CAMPAIGN PURPOSE: ${goal}
2. TONE & STYLE: ${tone}
3. INDUSTRY / AUDIENCE: ${companyType}
4. PRIMARY BRAND ACCENT COLOR: ${aiSelectedColor}
5. REQUIRED EMAIL SECTIONS:
${sectionsList.join('\n')}

============================================================
6. ZERO-HALLUCINATION SIGNATURE & SENDER RULES (CRITICAL):
============================================================
- The authorized sender for this email is:
  * Name: "${senderName}"
  * Title/Team: "${senderTitle}"
- STRICTLY FORBIDDEN:
  * NEVER invent or hallucinate fake names (e.g. DO NOT write "Alex", "Sarah", "John Doe", etc.).
  * NEVER invent fake company names (e.g. DO NOT write "Acclivity Solutions", "Apex Global", etc.).
  * NEVER invent fake email addresses, phone numbers, or fake physical addresses (e.g. DO NOT write "alex@acclivitysolutions.co", "(555) 123-4567", etc.).
  * NEVER add fake social media icon strips unless specifically requested.
- For the closing sign-off block, use EXACTLY this clean structure:
  Best regards,
  ${senderName}
  ${senderTitle}
- For the footer disclaimer (if enabled), use only a clean 1-line unsubscribe:
  <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 11px; color: #94a3b8;">
      You received this email regarding @{{ company | your team }}. <a href="#" style="color: #94a3b8; text-decoration: underline;">Unsubscribe</a>
  </div>

============================================================
7. STRICT ANTI-AI & HUMAN-CRAFTED WRITING RULES:
============================================================
- Write like an authentic, articulate human founder or business leader. NEVER sound robotic, synthetic, or salesy.
- STRICTLY FORBIDDEN:
  * Do NOT use long em-dashes (—). Use natural commas, periods, or simple hyphens instead.
  * Do NOT use cheesy overused emojis like rockets 🚀, fire 🔥, lightbulbs 💡, or sparkles ✨. Keep emojis absent or minimal and natural.
  * Do NOT use generic AI filler phrases such as: "I hope this email finds you well", "In today's fast-paced digital world", "game-changer", "revolutionize", "unlock", "supercharge", "delve", "look no further", "testament to".
- Use crisp, natural, conversational sentences focused directly on solving problems and delivering clear value.

============================================================
8. PERSONALIZATION MERGE TAGS (USE EXACTLY AS WRITTEN):
============================================================
- First Name: @{{ firstname | there }}
- Last Name: @{{ lastname | Partner }}
- Company Name: @{{ company | your team }}
- Email Address: @{{ email }}

============================================================
9. TECHNICAL EMAIL STANDARDS:
============================================================
- Max-width: 600px, centered using margin: 0 auto.
- Use robust table layout or container divs compatible with Gmail, Apple Mail, Outlook, and mobile screens.
- ALL CSS MUST BE 100% INLINE on HTML elements (e.g. style="..."). Do not use external or <style> blocks.
- Typography: modern system font stack (-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif).
- Ensure high contrast and dark body text (#0f172a / #334155) so the email is 100% legible across all dark mode and light mode mail clients.

${notes.trim() ? `============================================================\n10. SPECIFIC CONTENT BRIEF & OFFER DETAILS:\n` + notes.trim() + '\n' : ''}============================================================
OUTPUT INSTRUCTIONS:
============================================================
- Line 1 MUST be: SUBJECT: <Write an authentic, high-open-rate subject line incorporating merge tags, without cheesy words or rocket emojis>
- From Line 2 onwards, output ONLY the pure HTML email code starting from the outer wrapper <div style="..."> or <table ...>.
- Do NOT output any conversational text or markdown code blocks, ONLY the code.`;

        const promptArea = document.getElementById('aiGeneratedPromptText');
        if (promptArea) promptArea.value = prompt;
    }

    function copyAiPrompt() {
        const promptArea = document.getElementById('aiGeneratedPromptText');
        if (!promptArea) return;

        navigator.clipboard.writeText(promptArea.value).then(() => {
            const btn = document.getElementById('btnCopyAiPrompt');
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = `<i class="fas fa-check text-xs"></i> <span>Copied Prompt!</span>`;
                btn.classList.remove('bg-zinc-900', 'dark:bg-amber-100');
                btn.classList.add('bg-emerald-600', 'text-white');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('bg-emerald-600', 'text-white');
                    btn.classList.add('bg-zinc-900', 'dark:bg-amber-100');
                }, 2500);
            }
        }).catch(err => {
            promptArea.select();
            document.execCommand('copy');
            alert('Prompt copied to clipboard!');
        });
    }

    function launchAiTool(tool) {
        const prompt = document.getElementById('aiGeneratedPromptText')?.value || '';
        if (!prompt) return;

        // Auto copy to clipboard for user convenience
        navigator.clipboard.writeText(prompt).catch(() => {});

        if (tool === 'chatgpt') {
            const url = 'https://chatgpt.com/?q=' + encodeURIComponent(prompt);
            window.open(url, '_blank');
            showAiToast('ChatGPT opened with prompt pre-filled!');
        } else if (tool === 'claude') {
            window.open('https://claude.ai/new', '_blank');
            showAiToast('Prompt copied! Paste (Ctrl+V) into Claude.');
        } else if (tool === 'gemini') {
            window.open('https://gemini.google.com/app', '_blank');
            showAiToast('Prompt copied! Paste (Ctrl+V) into Gemini.');
        }
    }

    function showAiToast(msg) {
        const existing = document.getElementById('aiToastNotification');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'aiToastNotification';
        toast.className = 'fixed bottom-6 right-6 z-[1000000] px-4 py-2.5 bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 font-bold text-xs rounded-2xl shadow-2xl flex items-center gap-2 border border-zinc-700 dark:border-amber-300 animate-bounce';
        toast.innerHTML = `<i class="fas fa-check-circle text-emerald-500 text-sm"></i> <span>${escapeHtml(msg)}</span>`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3500);
    }

    function previewCustomHtml() {
        const rawCode = document.getElementById('customHtmlCodeInput')?.value || '';
        const rawSubject = document.getElementById('customHtmlSubjectInput')?.value || '';

        // Auto-extract subject if user pasted "SUBJECT: ..." at the top
        let cleanHtml = rawCode;
        if (cleanHtml.trim().startsWith('SUBJECT:')) {
            const lines = cleanHtml.split('\n');
            const extractedSubj = lines[0].replace('SUBJECT:', '').trim();
            if (extractedSubj && !rawSubject) {
                document.getElementById('customHtmlSubjectInput').value = extractedSubj;
            }
            cleanHtml = lines.slice(1).join('\n').trim();
        }

        // Clean any markdown fences if pasted
        cleanHtml = cleanHtml.replace(/^```html\s*/i, '').replace(/^```\s*/i, '').replace(/```\s*$/i, '');

        const renderedSubj = replaceMergeTags(document.getElementById('customHtmlSubjectInput')?.value || rawSubject || '(Custom Subject Line)', sampleLead);
        const renderedBody = replaceMergeTags(cleanHtml, sampleLead);

        const subjEl = document.getElementById('customHtmlLivePreviewSubject');
        const bodyEl = document.getElementById('customHtmlLivePreviewBody');

        if (subjEl) subjEl.textContent = renderedSubj;
        if (bodyEl) {
            bodyEl.innerHTML = renderedBody || '<p class="text-zinc-400 italic text-center py-8">Paste your HTML above to see the live rendering...</p>';
        }
    }

    function applyCustomHtmlToEditor() {
        let rawCode = document.getElementById('customHtmlCodeInput')?.value || '';
        let rawSubject = document.getElementById('customHtmlSubjectInput')?.value || '';

        // Clean subject if inside body
        if (rawCode.trim().startsWith('SUBJECT:')) {
            const lines = rawCode.split('\n');
            const extractedSubj = lines[0].replace('SUBJECT:', '').trim();
            if (extractedSubj && !rawSubject) {
                rawSubject = extractedSubj;
            }
            rawCode = lines.slice(1).join('\n').trim();
        }

        rawCode = rawCode.replace(/^```html\s*/i, '').replace(/^```\s*/i, '').replace(/```\s*$/i, '').trim();

        if (!rawCode) {
            alert('Please paste some HTML email code first!');
            return;
        }

        // Set subject
        if (rawSubject) {
            document.getElementById('subject_template').value = rawSubject;
        }

        // Set editor content
        window.setEditorContent(rawCode);

        closeTemplateLibrary();
    }

    function saveCustomHtmlTemplate() {
        let rawCode = document.getElementById('customHtmlCodeInput')?.value || '';
        let rawSubject = document.getElementById('customHtmlSubjectInput')?.value || '';

        rawCode = rawCode.replace(/^```html\s*/i, '').replace(/^```\s*/i, '').replace(/```\s*$/i, '').trim();
        if (!rawCode) {
            alert('Please paste some HTML email code to save!');
            return;
        }

        const templateName = prompt('Enter a name for this custom template:', 'My AI Custom Template');
        if (!templateName) return;

        const key = 'custom_' + Date.now();
        const customObj = {
            name: templateName,
            category: 'custom',
            categoryLabel: 'Custom & AI',
            icon: 'fa-wand-magic-sparkles',
            desc: 'User-created custom HTML template.',
            subject: rawSubject || 'Custom Broadcast Subject',
            body: rawCode
        };

        EMAIL_TEMPLATES[key] = customObj;

        // Save to localStorage
        try {
            const raw = localStorage.getItem('campaignstack_custom_templates');
            const customs = raw ? JSON.parse(raw) : {};
            customs[key] = customObj;
            localStorage.setItem('campaignstack_custom_templates', JSON.stringify(customs));
        } catch (e) {
            console.error(e);
        }

        alert(`Template "${templateName}" saved successfully!`);
        switchStudioMode('all');
        selectTemplateForModalPreview(key);
    }

    function deleteCustomTemplate(key) {
        if (!confirm('Are you sure you want to delete this custom template?')) return;
        delete EMAIL_TEMPLATES[key];
        try {
            const raw = localStorage.getItem('campaignstack_custom_templates');
            if (raw) {
                const customs = JSON.parse(raw);
                delete customs[key];
                localStorage.setItem('campaignstack_custom_templates', JSON.stringify(customs));
            }
        } catch (e) {
            console.error(e);
        }
    }

    function insertSnippet(type) {
        let snippet = '';

        if (type === 'cta_button') {
            snippet = `<div style="margin: 25px 0;"><a href="https://example.com" style="background-color: #0f172a; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">Click Here to Action &rarr;</a></div>`;
        } else if (type === 'quote_box') {
            snippet = `<div style="background-color: #f8fafc; border-left: 4px solid #f59e0b; padding: 14px 18px; border-radius: 6px; margin: 20px 0;"><p style="margin: 0; font-size: 14px; color: #334155; font-style: italic;">&ldquo;Insert notable quote, client proof, or critical announcement here.&rdquo;</p></div>`;
        } else if (type === 'signature') {
            snippet = `<p style="font-size: 14px; color: #475569; margin-top: 25px; line-height: 1.6;">Best regards,<br><strong style="color: #0f172a;">{{ Auth::user()->name ?? 'Operator' }}</strong><br>Campaign Stack Team</p>`;
        } else if (type === 'divider') {
            snippet = `<hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">`;
        }

        if (isCodeModeActive) {
            const textarea = document.getElementById('rawHtmlCodeEditor');
            textarea.value += '\n' + snippet;
        } else {
            window.insertContentToEditor(snippet);
        }
    }

    function insertMergeTag(tag) {
        const subjectInput = document.getElementById('subject_template');
        if (!subjectInput) return;

        const start = subjectInput.selectionStart || 0;
        const end = subjectInput.selectionEnd || 0;
        const text = subjectInput.value;
        subjectInput.value = text.substring(0, start) + tag + text.substring(end);
        subjectInput.focus();
        subjectInput.selectionStart = subjectInput.selectionEnd = start + tag.length;

        subjectInput.classList.add('ring-2', 'ring-amber-400', 'dark:ring-amber-300');
        setTimeout(() => subjectInput.classList.remove('ring-2', 'ring-amber-400', 'dark:ring-amber-300'), 500);
    }

    function insertEditorMergeTag(tag) {
        if (isCodeModeActive) {
            const textarea = document.getElementById('rawHtmlCodeEditor');
            textarea.value += tag;
        } else {
            window.insertContentToEditor(tag);
        }
    }

    const sampleLead = {
        salutation: "{{ $sampleContact->salutation ?? 'Mr.' }}",
        firstname: "{{ $sampleContact->firstname ?? 'Vikram' }}",
        lastname: "{{ $sampleContact->lastname ?? 'Aditya' }}",
        email: "{{ $sampleContact->email ?? 'vikram.aditya@razorpay.com' }}",
        company: "{{ $sampleContact->company ?? 'Razorpay Software' }}",
        mobile: "{{ $sampleContact->mobile ?? '+91 98201 44521' }}"
    };

    function replaceMergeTags(text, data) {
        if (!text) return '';
        return text.replace(/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\|\s*([^}]+?)\s*\}\}/g, function(match, field, fallback) {
            return data[field] || fallback.trim();
        }).replace(/\[\[([a-zA-Z_]+)\]\]/g, function(match, field) {
            return data[field] || match;
        });
    }

    let previewDeviceMode = 'desktop';

    function openLivePreview() {
        const subjectRaw = document.getElementById('subject_template').value;
        const bodyRaw = isCodeModeActive 
            ? document.getElementById('rawHtmlCodeEditor').value 
            : window.getEditorContent();

        const renderedSubject = replaceMergeTags(subjectRaw, sampleLead);
        const renderedBody = replaceMergeTags(bodyRaw, sampleLead);

        document.getElementById('previewModalSubject').textContent = renderedSubject || '(No Subject Line)';
        document.getElementById('previewModalBody').innerHTML = renderedBody || '<p class="text-zinc-400 italic">No email body content entered yet.</p>';
        
        // Apply current device mode classes
        setPreviewDevice(previewDeviceMode || 'desktop');

        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closeLivePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    function setPreviewDevice(device) {
        previewDeviceMode = device;
        const frame = document.getElementById('previewDeviceFrame');
        const btnDesktop = document.getElementById('btnPrevDesktop');
        const btnMobile = document.getElementById('btnPrevMobile');
        const notch = document.getElementById('mobilePreviewNotch');
        const bodyContainer = document.getElementById('previewModalBodyContainer');

        if (!frame || !btnDesktop || !btnMobile) return;

        if (device === 'mobile') {
            frame.className = 'w-full max-w-[380px] h-full flex flex-col min-h-0 bg-zinc-900 dark:bg-black p-3.5 rounded-[36px] border-[5px] border-zinc-800 dark:border-zinc-700 shadow-2xl space-y-2.5 font-sans transition-all';
            btnMobile.className = 'px-3 py-1 bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 font-bold rounded-lg text-xs cursor-pointer shadow-2xs';
            btnDesktop.className = 'px-3 py-1 bg-slate-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold rounded-lg text-xs cursor-pointer hover:bg-slate-200';
            if (notch) notch.classList.remove('hidden');
            if (bodyContainer) bodyContainer.className = 'flex-1 min-h-0 overflow-y-auto bg-white text-zinc-900 p-3.5 rounded-2xl border border-slate-200 shadow-xs text-xs';
        } else {
            frame.className = 'w-full h-full flex flex-col min-h-0 bg-slate-100 dark:bg-[#09090B] p-4 rounded-2xl border border-slate-200 dark:border-zinc-800 space-y-3 font-sans transition-all';
            btnDesktop.className = 'px-3 py-1 bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 font-bold rounded-lg text-xs cursor-pointer shadow-2xs';
            btnMobile.className = 'px-3 py-1 bg-slate-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold rounded-lg text-xs cursor-pointer hover:bg-slate-200';
            if (notch) notch.classList.add('hidden');
            if (bodyContainer) bodyContainer.className = 'flex-1 min-h-0 overflow-y-auto bg-white text-zinc-900 p-5 rounded-xl border border-slate-200 shadow-xs';
        }
    }

    function updateAudienceCount() {
        const checkedBoxes = Array.from(document.querySelectorAll('.tag-checkbox:checked'));
        let count = 0;
        checkedBoxes.forEach(cb => {
            count += parseInt(cb.getAttribute('data-count') || 0);
        });

        const display = document.getElementById('estimatedAudienceDisplay');
        if (display) {
            if (checkedBoxes.length === 0) {
                display.textContent = 'All Active Leads (No filter)';
            } else {
                display.textContent = `~${count} targeted contact(s)`;
            }
        }
    }

    function escapeHtml(string) {
        return String(string).replace(/[&<>"'`=\/]/g, function (s) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
            }[s];
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateAudienceCount();

        // Teleport modals directly to <body> to bypass any parent CSS transform stacking contexts
        const tplModal = document.getElementById('templateLibraryModal');
        const prevModal = document.getElementById('previewModal');
        if (tplModal && tplModal.parentElement !== document.body) {
            document.body.appendChild(tplModal);
        }
        if (prevModal && prevModal.parentElement !== document.body) {
            document.body.appendChild(prevModal);
        }
    });
    // Ensure form submit syncs final editor HTML
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('newsletterForm');
        if (form) {
            form.addEventListener('submit', function() {
                if (isCodeModeActive) {
                    document.getElementById('body_template').value = document.getElementById('rawHtmlCodeEditor').value;
                } else if (window.getEditorContent) {
                    document.getElementById('body_template').value = window.getEditorContent();
                }
            });
        }
    });
</script>


<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
<script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1 space-x-2">
                    <a href="/newsletters" class="hover:text-zinc-900 dark:hover:text-amber-200 transition-colors">Newsletters</a>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-white font-semibold">
                        {{ empty($newsletter->id) ? 'Create Broadcast' : 'Edit Broadcast' }}
                    </span>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas {{ empty($newsletter->id) ? 'fa-pen-fancy' : 'fa-edit' }} text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ empty($newsletter->id) ? __('Compose Email Broadcast') : __('Edit Broadcast: ' . $newsletter->title) }}
                    </h2>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Choose from Pre-built Templates Button -->
                <button type="button" onclick="openTemplateLibrary();" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-zinc-950 font-bold text-xs rounded-xl shadow-2xs transition-all gap-2 cursor-pointer">
                    <i class="fas fa-layer-group text-xs"></i>
                    <span>Template Library</span>
                </button>

                <!-- Live Preview Button -->
                <button type="button" onclick="openLivePreview();" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#141417] text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-50 transition-colors gap-2 shadow-2xs">
                    <i class="fas fa-eye text-2xs text-amber-500"></i>
                    <span>Live Preview</span>
                </button>
                <a href="/newsletters" class="inline-flex items-center px-3.5 py-2 bg-slate-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-colors gap-2">
                    <i class="fas fa-arrow-left text-2xs"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-12 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form name="save-newsletter" action="/savenewsletter" method="post" onsubmit="if(isCodeModeActive) { document.getElementById('body_template').value = document.getElementById('rawHtmlCodeEditor').value; }">
                @csrf
                <input type="hidden" name="newsletter_id" value="{{ $newsletter->id }}" />

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                    <!-- MAIN COMPOSER COLUMN (8 cols) -->
                    <div class="lg:col-span-8 space-y-6">

                        <!-- Subject Line & Title Card -->
                        <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-envelope-open-text text-amber-500/80 dark:text-amber-300/80"></i>
                                    Subject Line &amp; Broadcast Name
                                </h3>
                            </div>

                            <!-- Subject Template Field -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider" for="subject_template">
                                        Email Subject Line <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-3xs text-zinc-400">Click tags below to insert into subject</span>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-sm">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <input class="w-full pl-10 pr-4 py-3 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300 focus:ring-amber-300 text-sm font-semibold" 
                                           id="subject_template" name="subject_template" type="text" value="{{ $newsletter->subject_template }}" placeholder="e.g. @{{ firstname | Founder }}, scaling outreach for @{{ company | your team }}" required autofocus>
                                </div>

                                <!-- 1-Click Subject Merge Tag Inserters -->
                                <div class="flex items-center flex-wrap gap-1.5 mt-2.5">
                                    <span class="text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mr-1">
                                        Insert Tags:
                                    </span>
                                    <button type="button" onclick="insertMergeTag('@{{ firstname | there }}')" class="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-500/30 text-3xs font-mono font-bold hover:bg-amber-100 transition-colors">
                                        + @{{ firstname }}
                                    </button>
                                    <button type="button" onclick="insertMergeTag('@{{ lastname | Colleague }}')" class="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-500/30 text-3xs font-mono font-bold hover:bg-amber-100 transition-colors">
                                        + @{{ lastname }}
                                    </button>
                                    <button type="button" onclick="insertMergeTag('@{{ company | your company }}')" class="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-500/30 text-3xs font-mono font-bold hover:bg-amber-100 transition-colors">
                                        + @{{ company }}
                                    </button>
                                    <button type="button" onclick="insertMergeTag('@{{ salutation | Hi }}')" class="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-500/30 text-3xs font-mono font-bold hover:bg-amber-100 transition-colors">
                                        + @{{ salutation }}
                                    </button>
                                </div>
                            </div>

                            <!-- Internal Title -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="title">
                                    Internal Campaign / Broadcast Title <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-sm">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <input class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300 focus:ring-amber-300 text-xs font-medium" 
                                           id="title" name="title" type="text" value="{{ $newsletter->title }}" placeholder="e.g. Q3 Outreach Sequence" required>
                                </div>
                            </div>
                        </div>

                        <!-- DUAL-MODE EMAIL BODY STUDIO (WYSIWYG Visual + HTML Code View) -->
                        <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 space-y-4">
                            
                            <!-- Editor Header: Mode Switcher + Smart Snippets -->
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                        <i class="fas fa-paint-brush text-amber-500/80 dark:text-amber-300/80"></i>
                                        Email Content Studio
                                    </h3>
                                </div>

                                <!-- Visual / Code Mode Switcher -->
                                <div class="flex items-center gap-1 bg-slate-100 dark:bg-[#09090B] p-1 rounded-xl border border-slate-200 dark:border-zinc-800">
                                    <button type="button" id="btnModeVisual" onclick="toggleEditorMode('visual')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 shadow-2xs transition-all">
                                        <i class="fas fa-eye text-3xs mr-1"></i> Visual Editor
                                    </button>
                                    <button type="button" id="btnModeCode" onclick="toggleEditorMode('code')" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-all">
                                        <i class="fas fa-code text-3xs mr-1"></i> HTML Source
                                    </button>
                                </div>
                            </div>

                            <!-- Smart Component Snippet Inserters -->
                            <div class="flex items-center flex-wrap gap-2 p-2.5 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 text-xs">
                                <span class="text-3xs font-bold text-zinc-400 uppercase tracking-wider mr-1">Quick Components:</span>
                                
                                <button type="button" onclick="insertSnippet('cta_button')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-800 hover:border-amber-300 text-2xs font-semibold flex items-center gap-1.5 shadow-2xs">
                                    <i class="fas fa-mouse-pointer text-amber-500 text-3xs"></i>
                                    <span>CTA Button</span>
                                </button>
                                
                                <button type="button" onclick="insertSnippet('quote_box')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-800 hover:border-amber-300 text-2xs font-semibold flex items-center gap-1.5 shadow-2xs">
                                    <i class="fas fa-quote-left text-amber-500 text-3xs"></i>
                                    <span>Quote / Callout</span>
                                </button>

                                <button type="button" onclick="insertSnippet('signature')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-800 hover:border-amber-300 text-2xs font-semibold flex items-center gap-1.5 shadow-2xs">
                                    <i class="fas fa-signature text-amber-500 text-3xs"></i>
                                    <span>Sign-off Block</span>
                                </button>

                                <button type="button" onclick="insertSnippet('divider')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-800 hover:border-amber-300 text-2xs font-semibold flex items-center gap-1.5 shadow-2xs">
                                    <i class="fas fa-minus text-zinc-400 text-3xs"></i>
                                    <span>Divider</span>
                                </button>

                                <div class="h-4 w-px bg-slate-200 dark:bg-zinc-800 mx-1"></div>

                                <button type="button" onclick="insertEditorMergeTag('@{{ salutation | Hi }} @{{ firstname | Friend }}')" class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200/80 dark:border-amber-500/30 text-3xs font-mono font-bold">
                                    + Salutation
                                </button>
                                <button type="button" onclick="insertEditorMergeTag('@{{ company | your team }}')" class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200/80 dark:border-amber-500/30 text-3xs font-mono font-bold">
                                    + Company
                                </button>
                            </div>

                            <!-- Visual WYSIWYG Container (Clean Zero-Badge Studio) -->
                            <div id="visualEditorContainer">
                                <textarea id="body_template" name="body_template">{{ $newsletter->body_template }}</textarea>
                            </div>

                            <!-- Raw HTML Code Editor Container -->
                            <div id="codeEditorContainer" class="hidden">
                                <div class="rounded-xl overflow-hidden border border-zinc-800 bg-[#09090B]">
                                    <div class="bg-[#141417] px-4 py-2 border-b border-zinc-800 flex items-center justify-between text-3xs font-mono text-zinc-400 select-none">
                                        <span>HTML Source Studio</span>
                                        <span class="text-amber-400 font-bold">Clean Inline CSS Supported</span>
                                    </div>
                                    <textarea id="rawHtmlCodeEditor" class="w-full p-4 bg-[#09090B] text-zinc-200 font-mono text-xs leading-relaxed focus:outline-none min-h-[480px] resize-y border-0"></textarea>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- SIDEBAR / SETTINGS COLUMN (4 cols) -->
                    <div class="lg:col-span-4 space-y-6">

                        <!-- Broadcast Settings Card -->
                        <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-sliders-h text-amber-500/80 dark:text-amber-300/80"></i>
                                    Broadcast Setup
                                </h4>
                            </div>

                            <!-- Campaign Selector -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="campaign_id">
                                    Strategic Campaign Folder <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-sm">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <select class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white shadow-2xs focus:border-amber-300 focus:ring-amber-300 text-xs font-semibold" 
                                            id="campaign_id" name="campaign_id" required>
                                        <option value="">Select Campaign</option>
                                        @foreach($campaigns as $camp)
                                            <option value="{{ $camp->id }}" @if($newsletter->campaign_id == $camp->id) selected @endif>{{ $camp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status Selector -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="status">
                                    Workflow State <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-sm">
                                        <i class="fas fa-toggle-on"></i>
                                    </div>
                                    <select class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white shadow-2xs focus:border-amber-300 focus:ring-amber-300 text-xs font-semibold" 
                                            id="status" name="status" required>
                                        <option value="D" @if($newsletter->status == 'D' || empty($newsletter->status)) selected @endif>Draft (Not queued)</option>
                                        <option value="N" @if($newsletter->status == 'N') selected @endif>Ready (Ready for Dispatch Studio)</option>
                                        <option value="Q" @if($newsletter->status == 'Q') selected @endif>Queuing (In delivery)</option>
                                        <option value="S" @if($newsletter->status == 'S') selected @endif>Sent (Completed)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Outbound Sending Servers (SMTP Selection) -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-2">
                                    Sender Gateways (Outbound SMTP)
                                </label>
                                @php
                                    $assignedAccountIds = $newsletter->newsletter_outbound_mail_accounts ? $newsletter->newsletter_outbound_mail_accounts->pluck('outbound_mail_account_id')->toArray() : [];
                                @endphp
                                @if(count($outboundAccounts) > 0)
                                    <div class="space-y-2 max-h-36 overflow-y-auto p-1">
                                        @foreach($outboundAccounts as $acc)
                                            @php $isAccChecked = in_array($acc->id, $assignedAccountIds); @endphp
                                            <label class="cursor-pointer flex items-center justify-between p-2.5 rounded-xl border transition-all text-xs select-none {{ $isAccChecked ? 'bg-amber-50 border-amber-300 dark:bg-amber-950/40 dark:border-amber-500/40 text-amber-900 dark:text-amber-200' : 'bg-slate-50 dark:bg-[#09090B] border-slate-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300' }}">
                                                <div class="flex items-center gap-2 truncate">
                                                    <input type="checkbox" name="outbound_account_ids[]" value="{{ $acc->id }}" {{ $isAccChecked ? 'checked' : '' }} class="rounded border-zinc-300 text-amber-500 focus:ring-amber-400">
                                                    <span class="truncate font-semibold">{{ $acc->name }}</span>
                                                </div>
                                                <span class="text-3xs font-mono opacity-75 px-1.5 py-0.5 rounded bg-white dark:bg-black/40">{{ $acc->type }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 bg-amber-50 text-amber-800 rounded-xl text-xs">
                                        No outbound accounts configured. <a href="/account-form/new" class="underline font-bold">Add SMTP</a>
                                    </div>
                                @endif
                            </div>

                            <!-- Target Tags Selector with Live Counter -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider">
                                        Target Audience Segments
                                    </label>
                                    <span id="estimatedAudienceDisplay" class="text-3xs font-bold text-amber-600 dark:text-amber-300 font-mono">
                                        Calculating...
                                    </span>
                                </div>
                                @php
                                    $assignedTagIds = $newsletter->newsletter_tags ? $newsletter->newsletter_tags->pluck('tag_id')->toArray() : [];
                                @endphp
                                @if(count($tags) > 0)
                                    <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto p-1">
                                        @foreach($tags as $tag)
                                            @php $isChecked = in_array($tag->id, $assignedTagIds); @endphp
                                            <label class="cursor-pointer flex items-center justify-between p-2 rounded-xl border transition-all text-xs font-semibold select-none {{ $isChecked ? 'bg-amber-50 border-amber-300 dark:bg-amber-950/40 dark:border-amber-500/40 text-amber-900 dark:text-amber-200' : 'bg-slate-50 dark:bg-[#09090B] border-slate-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300' }}">
                                                <div class="flex items-center gap-2 truncate">
                                                    <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" data-count="{{ $tag->contacts_count ?? 0 }}" onchange="updateAudienceCount()" {{ $isChecked ? 'checked' : '' }} class="tag-checkbox rounded border-zinc-300 text-amber-500 focus:ring-amber-400">
                                                    <span class="truncate">{{ $tag->label }}</span>
                                                </div>
                                                <span class="text-3xs font-mono font-bold px-1.5 py-0.5 rounded-md bg-white dark:bg-[#141417] text-zinc-500 border border-slate-200 dark:border-zinc-800">
                                                    {{ $tag->contacts_count ?? 0 }} leads
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Action Bar -->
                            <div class="flex items-center justify-end space-x-3 pt-5 border-t border-zinc-100 dark:border-zinc-800">
                                <a href="/newsletters" class="px-5 py-2.5 bg-slate-100 dark:bg-[#141417] hover:bg-slate-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold text-xs rounded-xl border border-slate-200 dark:border-zinc-800 transition-colors">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 font-bold text-xs rounded-xl shadow-2xs transition-all gap-2 cursor-pointer">
                                    <i class="fas fa-check text-xs"></i>
                                    <span>Save Broadcast</span>
                                </button>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- PRE-BUILT EMAIL TEMPLATE LIBRARY & PREVIEW MODAL -->
    <div id="templateLibraryModal" class="fixed inset-0 z-[999999] hidden bg-black/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6">
        <div class="bg-white dark:bg-[#141417] rounded-3xl max-w-5xl w-full h-[85vh] max-h-[85vh] flex flex-col p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-zinc-800 transform transition-all">
            
            <!-- Modal Header (Pinned) -->
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-lg border border-amber-200/60 dark:border-amber-500/30">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Email Template Studio</h3>
                        <p class="text-2xs text-zinc-500 dark:text-zinc-400">Choose from generic &amp; high-converting modern email templates with instant live preview.</p>
                    </div>
                </div>
                <button type="button" onclick="closeTemplateLibrary()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-2 cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Category Filter Tabs (Pinned) -->
            <div class="flex items-center flex-wrap gap-1.5 py-2.5 border-b border-zinc-100 dark:border-zinc-800/60 shrink-0">
                <button type="button" id="filterBtn_all" onclick="filterTemplates('all')" class="tpl-filter-btn px-3 py-1.5 text-xs font-bold rounded-xl bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 shadow-2xs cursor-pointer">
                    All Templates
                </button>
                <button type="button" id="filterBtn_generic" onclick="filterTemplates('generic')" class="tpl-filter-btn px-3 py-1.5 text-xs font-semibold rounded-xl text-zinc-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                    Generic &amp; Clean
                </button>
                <button type="button" id="filterBtn_sales" onclick="filterTemplates('sales')" class="tpl-filter-btn px-3 py-1.5 text-xs font-semibold rounded-xl text-zinc-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                    Cold Outreach &amp; Sales
                </button>
                <button type="button" id="filterBtn_content" onclick="filterTemplates('content')" class="tpl-filter-btn px-3 py-1.5 text-xs font-semibold rounded-xl text-zinc-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                    Newsletters &amp; Digest
                </button>
                <button type="button" id="filterBtn_events" onclick="filterTemplates('events')" class="tpl-filter-btn px-3 py-1.5 text-xs font-semibold rounded-xl text-zinc-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                    Events &amp; Feedback
                </button>
                <button type="button" id="filterBtn_custom" onclick="filterTemplates('custom')" class="tpl-filter-btn px-3 py-1.5 text-xs font-semibold rounded-xl text-zinc-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                    My Custom Saved
                </button>

                <!-- AI Prompt & Custom HTML Mode Tab -->
                <button type="button" id="filterBtn_ai_custom" onclick="switchStudioMode('ai_custom')" class="tpl-filter-btn px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-amber-500/20 to-amber-600/20 border border-amber-400/50 text-amber-700 dark:text-amber-300 hover:from-amber-500/30 hover:to-amber-600/30 transition-all flex items-center gap-1.5 cursor-pointer ml-auto">
                    <i class="fas fa-wand-magic-sparkles text-amber-500 text-3xs"></i>
                    <span>✨ AI Prompt &amp; Custom HTML Studio</span>
                </button>
            </div>

            <!-- VIEW 1: STANDARD TEMPLATES BROWSER -->
            <div id="standardTemplatesView" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start flex-1 min-h-0 py-3 overflow-hidden">
                
                <!-- Left: Template Cards List (Scrolls Internally) -->
                <div id="templateCardsContainer" class="lg:col-span-5 space-y-2.5 h-full overflow-y-auto pr-1">
                    <!-- Dynamically populated via JS -->
                </div>

                <!-- Right: Live Interactive Preview of Selected Template (Scrolls Internally) -->
                <div class="lg:col-span-7 bg-slate-50 dark:bg-[#09090B] rounded-2xl border border-slate-200 dark:border-zinc-800 p-4 space-y-3 h-full flex flex-col min-h-0">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-zinc-800 pb-2.5 shrink-0">
                        <div>
                            <span class="text-3xs font-bold text-zinc-400 uppercase tracking-wider block">Selected Template</span>
                            <h4 id="modalPreviewName" class="font-bold text-sm text-zinc-900 dark:text-white">Clean Minimal Letter</h4>
                        </div>
                        <button type="button" id="btnApplySelectedTemplate" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-2xs transition-all gap-1.5 cursor-pointer">
                            <i class="fas fa-check text-2xs"></i>
                            <span>Load into Editor</span>
                        </button>
                    </div>

                    <!-- Subject Preview (Shrink-0) -->
                    <div class="shrink-0">
                        <span class="text-3xs font-bold text-zinc-400 uppercase tracking-wider block mb-1">Subject Preview:</span>
                        <div id="modalPreviewSubject" class="p-2.5 bg-white dark:bg-[#141417] rounded-xl border border-slate-200 dark:border-zinc-800 text-xs font-bold text-zinc-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Body Preview Canvas (True Email Canvas - Flex-1 Scrollable) -->
                    <div class="flex-1 min-h-0 flex flex-col">
                        <span class="text-3xs font-bold text-zinc-400 uppercase tracking-wider block mb-1 shrink-0">Body Preview:</span>
                        <div class="flex-1 min-h-0 overflow-y-auto p-4 bg-white text-zinc-900 rounded-xl border border-slate-200 shadow-xs">
                            <div id="modalPreviewBody" class="text-xs text-zinc-900 leading-relaxed font-sans"></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- VIEW 2: AI PROMPT GENERATOR & CUSTOM HTML IMPORTER STUDIO -->
            <div id="aiCustomStudioView" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-5 items-start flex-1 min-h-0 py-3 overflow-hidden">
                
                <!-- Left: AI Prompt Generator Studio (Col 6) -->
                <div class="lg:col-span-6 bg-slate-50 dark:bg-[#09090B] rounded-2xl border border-slate-200 dark:border-zinc-800 p-4 space-y-3.5 h-full overflow-y-auto">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-zinc-800 pb-2.5">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-600 dark:text-amber-300 font-black text-xs flex items-center justify-center">1</span>
                            <h4 class="font-bold text-xs text-zinc-900 dark:text-white">Generate Prompt for ChatGPT / Gemini / Claude</h4>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider mb-1">Email Purpose / Goal</label>
                            <select id="aiPromptGoal" onchange="updateGeneratedAiPrompt()" class="w-full px-2.5 py-1.5 bg-white dark:bg-[#141417] border border-slate-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                                <option value="Cold Sales Outreach & B2B Lead Gen">💼 Cold Sales Outreach &amp; Lead Gen</option>
                                <option value="Product Launch & Feature Announcement">🚀 Product Launch / Announcement</option>
                                <option value="Weekly Newsletter & Curated Digest">📰 Weekly Newsletter / Digest</option>
                                <option value="Limited-Time Special Offer & Discount Promo">🏷️ Limited-Time Offer / Discount</option>
                                <option value="VIP Event & Webinar Invitation">🎟️ VIP Event / Webinar Invite</option>
                                <option value="Customer Re-engagement & Winback">🔄 Customer Re-engagement</option>
                                <option value="Plain Human-to-Human Minimal Letter">✉️ Minimal Clean Text Letter</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider mb-1">Tone &amp; Style</label>
                            <select id="aiPromptTone" onchange="updateGeneratedAiPrompt()" class="w-full px-2.5 py-1.5 bg-white dark:bg-[#141417] border border-slate-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                                <option value="Clean, Modern, and Highly-Converting">✨ Clean, Modern &amp; Converting</option>
                                <option value="Executive, Authoritative and Professional">👔 Executive &amp; Professional</option>
                                <option value="Friendly, Warm and Casual">☕ Friendly, Warm &amp; Casual</option>
                                <option value="Bold, Punchy and High-Energy">⚡ Bold &amp; High-Energy</option>
                                <option value="Ultra-Minimalist Plain Text">📝 Ultra-Minimalist</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider mb-1">Target Audience / Niche</label>
                            <input type="text" id="aiPromptIndustry" oninput="updateGeneratedAiPrompt()" placeholder="e.g. SaaS Founders, Marketing Leads" value="SaaS Founders & CTOs" class="w-full px-2.5 py-1.5 bg-white dark:bg-[#141417] border border-slate-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200">
                        </div>

                        <div>
                            <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider mb-1">Brand Accent Color</label>
                            <div class="flex items-center gap-1.5 pt-0.5">
                                <button type="button" onclick="setAiColor('#f59e0b', this)" class="ai-color-btn w-6 h-6 rounded-full bg-amber-500 ring-2 ring-amber-500 scale-110 cursor-pointer" title="Amber"></button>
                                <button type="button" onclick="setAiColor('#4f46e5', this)" class="ai-color-btn w-6 h-6 rounded-full bg-indigo-600 cursor-pointer" title="Indigo"></button>
                                <button type="button" onclick="setAiColor('#10b981', this)" class="ai-color-btn w-6 h-6 rounded-full bg-emerald-600 cursor-pointer" title="Emerald"></button>
                                <button type="button" onclick="setAiColor('#f43f5e', this)" class="ai-color-btn w-6 h-6 rounded-full bg-rose-500 cursor-pointer" title="Rose"></button>
                                <button type="button" onclick="setAiColor('#0f172a', this)" class="ai-color-btn w-6 h-6 rounded-full bg-slate-900 dark:bg-zinc-100 cursor-pointer" title="Dark / Mono"></button>
                                <input type="text" id="aiCustomHexColor" oninput="aiSelectedColor = this.value; updateGeneratedAiPrompt();" value="#f59e0b" class="w-18 px-1.5 py-0.5 text-3xs font-mono bg-white dark:bg-[#141417] border border-slate-200 dark:border-zinc-800 rounded-md text-zinc-700 dark:text-zinc-300">
                            </div>
                        </div>
                    </div>

                    <!-- Required Sections Checkboxes -->
                    <div>
                        <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider mb-1.5">Include Sections:</label>
                        <div class="grid grid-cols-3 gap-1.5 text-3xs text-zinc-700 dark:text-zinc-300">
                            <label class="flex items-center space-x-1.5 cursor-pointer">
                                <input type="checkbox" id="aiSecHero" onchange="updateGeneratedAiPrompt()" checked class="rounded border-zinc-300 text-amber-500 focus:ring-amber-400">
                                <span>Hero Banner</span>
                            </label>
                            <label class="flex items-center space-x-1.5 cursor-pointer">
                                <input type="checkbox" id="aiSecBullets" onchange="updateGeneratedAiPrompt()" checked class="rounded border-zinc-300 text-amber-500 focus:ring-amber-400">
                                <span>Feature Bullets</span>
                            </label>
                            <label class="flex items-center space-x-1.5 cursor-pointer">
                                <input type="checkbox" id="aiSecQuote" onchange="updateGeneratedAiPrompt()" checked class="rounded border-zinc-300 text-amber-500 focus:ring-amber-400">
                                <span>Quote Box</span>
                            </label>
                            <label class="flex items-center space-x-1.5 cursor-pointer">
                                <input type="checkbox" id="aiSecCta" onchange="updateGeneratedAiPrompt()" checked class="rounded border-zinc-300 text-amber-500 focus:ring-amber-400">
                                <span>Action CTA</span>
                            </label>
                            <label class="flex items-center space-x-1.5 cursor-pointer">
                                <input type="checkbox" id="aiSecFooter" onchange="updateGeneratedAiPrompt()" checked class="rounded border-zinc-300 text-amber-500 focus:ring-amber-400">
                                <span>Footer Block</span>
                            </label>
                        </div>
                    </div>

                    <!-- Real Sender Persona (Prevents AI Hallucinations) -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider mb-1">Your Sender Name (No AI Fakes)</label>
                            <input type="text" id="aiSenderName" oninput="updateGeneratedAiPrompt()" value="{{ Auth::user()->name ?? 'Founder' }}" placeholder="e.g. Arjun Sharma" class="w-full px-2.5 py-1.5 bg-white dark:bg-[#141417] border border-slate-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                        </div>

                        <div>
                            <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider mb-1">Your Title / Team</label>
                            <input type="text" id="aiSenderTitle" oninput="updateGeneratedAiPrompt()" value="Founder &bull; Campaign Team" placeholder="e.g. Founder &bull; CarvingIT" class="w-full px-2.5 py-1.5 bg-white dark:bg-[#141417] border border-slate-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                        </div>
                    </div>

                    <!-- Custom Brief / Notes -->
                    <div>
                        <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider mb-1">Custom Notes / Offer Brief (Optional)</label>
                        <input type="text" id="aiPromptNotes" oninput="updateGeneratedAiPrompt()" placeholder="e.g. Mention 20% discount on annual plan with promo code AUTOMATE20" class="w-full px-2.5 py-1.5 bg-white dark:bg-[#141417] border border-slate-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200">
                    </div>

                    <!-- Generated Prompt Box -->
                    <div class="space-y-1.5 pt-1">
                        <div class="flex items-center justify-between">
                            <span class="text-3xs font-bold text-zinc-400 uppercase tracking-wider">Human-Crafted Master Prompt</span>
                            <span class="text-3xs text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                <i class="fas fa-shield-alt text-3xs"></i> Anti-AI Filtered
                            </span>
                        </div>

                        <textarea id="aiGeneratedPromptText" rows="5" readonly class="w-full p-2.5 font-mono text-3xs bg-slate-900 text-amber-300 border border-zinc-700 rounded-xl leading-relaxed focus:outline-none select-all"></textarea>
                        
                        <!-- 1-Click Launch Directly into AI Model with Logos -->
                        <div class="space-y-1.5 pt-1">
                            <div class="grid grid-cols-3 gap-2">
                                <!-- ChatGPT with Pre-fill -->
                                <button type="button" onclick="launchAiTool('chatgpt')" class="px-2.5 py-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-600/40 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-2xs cursor-pointer group" title="Open ChatGPT with prompt pre-filled">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.2599 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7475-7.0729zm-9.022 12.6081a4.4755 4.4755 0 0 1-2.8764-1.0408l.1419-.0804 4.7783-2.7582a.7948.7948 0 0 0 .3927-.6813v-6.7369l2.02 1.1683a.071.071 0 0 1 .038.052v5.5826a4.5045 4.5045 0 0 1-4.4945 4.4947zm-9.6607-4.1254a4.4708 4.4708 0 0 1-.5346-3.0137l.142.0852 4.783 2.7582a.7712.7712 0 0 0 .7806 0l5.8428-3.3685v2.3324a.0804.0804 0 0 1-.0332.0615L9.74 19.9502a4.4992 4.4992 0 0 1-6.1408-1.6464zM2.3408 7.8956a4.485 4.485 0 0 1 2.3655-1.9728V11.6a.7664.7664 0 0 0 .3879.6765l5.8144 3.3543-2.0201 1.1683a.0757.0757 0 0 1-.071 0l-4.8303-2.7866A4.504 4.504 0 0 1 2.3408 7.872zm16.5963 3.8558L13.1038 8.364 15.1192 7.2a.0757.0757 0 0 1 .071 0l4.8303 2.7913a4.4944 4.4944 0 0 1-.6765 8.1042v-5.6772a.79.79 0 0 0-.407-.667zM20.08 6.425a4.4708 4.4708 0 0 1 .5346 3.0137l-.142-.0852-4.783-2.7582a.7712.7712 0 0 0-.7806 0L9.0662 9.9638V7.6314a.0804.0804 0 0 1 .0332-.0615l4.9443-2.8538A4.4992 4.4992 0 0 1 20.08 6.425zM8.8872 13.0645l-2.02-1.1683a.071.071 0 0 1-.038-.052V6.2616a4.5045 4.5045 0 0 1 7.371-3.4539l-.142.0805-4.7783 2.7582a.7948.7948 0 0 0-.3927.6813zm1.1073-2.3655l2.6025-1.4998 2.6025 1.4998v3.0044l-2.6025 1.4998-2.6025-1.4998z"/>
                                    </svg>
                                    <span class="truncate">ChatGPT</span>
                                </button>

                                <!-- Claude with Direct Launch -->
                                <button type="button" onclick="launchAiTool('claude')" class="px-2.5 py-2 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/50 text-amber-900 dark:text-amber-200 border border-amber-300 dark:border-amber-600/40 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-2xs cursor-pointer group" title="Copy prompt and open Claude">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2L9.5 9.5 2 12l7.5 2.5L12 22l2.5-7.5L22 12l-7.5-2.5z"/>
                                    </svg>
                                    <span class="truncate">Claude</span>
                                </button>

                                <!-- Gemini with Direct Launch -->
                                <button type="button" onclick="launchAiTool('gemini')" class="px-2.5 py-2 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-900 dark:text-blue-200 border border-blue-300 dark:border-blue-600/40 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-2xs cursor-pointer group" title="Copy prompt and open Google Gemini">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 0C12 6.627 6.627 12 0 12c6.627 0 12 5.373 12 12 0-6.627 5.373-12 12-12-6.627 0-12-5.373-12-12z"/>
                                    </svg>
                                    <span class="truncate">Gemini</span>
                                </button>
                            </div>

                            <button type="button" id="btnCopyAiPrompt" onclick="copyAiPrompt()" class="w-full py-2 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 font-bold text-xs rounded-xl shadow-2xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fas fa-copy text-xs"></i>
                                <span>Copy Master Prompt to Clipboard</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right: Custom HTML Code Input & Live Render Canvas (Col 6) -->
                <div class="lg:col-span-6 bg-slate-50 dark:bg-[#09090B] rounded-2xl border border-slate-200 dark:border-zinc-800 p-4 space-y-3 h-full flex flex-col min-h-0">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-zinc-800 pb-2 shrink-0">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-300 font-black text-xs flex items-center justify-center">2</span>
                            <h4 class="font-bold text-xs text-zinc-900 dark:text-white">Paste AI-Generated HTML &amp; Instant Live Preview</h4>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="saveCustomHtmlTemplate()" class="px-3 py-1.5 bg-slate-200 dark:bg-zinc-800 hover:bg-slate-300 text-zinc-800 dark:text-zinc-200 font-bold text-3xs rounded-xl transition-colors cursor-pointer" title="Save as reusable template">
                                <i class="fas fa-bookmark text-amber-500 mr-1"></i> Save as Reusable
                            </button>
                            <button type="button" onclick="applyCustomHtmlToEditor()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-2xs transition-all gap-1 cursor-pointer">
                                <i class="fas fa-arrow-down text-2xs mr-1"></i> Load into Broadcast
                            </button>
                        </div>
                    </div>

                    <!-- Subject Line Input -->
                    <div class="shrink-0">
                        <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider mb-1">Subject Line</label>
                        <input type="text" id="customHtmlSubjectInput" oninput="previewCustomHtml()" placeholder="e.g. Special Offer regarding @{{ company | your team }}" class="w-full px-3 py-1.5 bg-white dark:bg-[#141417] border border-slate-200 dark:border-zinc-800 rounded-xl text-xs font-bold text-zinc-900 dark:text-white">
                    </div>

                    <!-- HTML Paste Box (Shrink-0) -->
                    <div class="shrink-0">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-bold text-3xs text-zinc-500 uppercase tracking-wider">Paste HTML Code Here:</label>
                            <span class="text-3xs text-zinc-400">Renders live below automatically</span>
                        </div>
                        <textarea id="customHtmlCodeInput" rows="5" oninput="previewCustomHtml()" placeholder="Paste the HTML code returned by ChatGPT / Gemini / Claude here..." class="w-full p-2.5 font-mono text-3xs bg-slate-950 text-emerald-400 border border-zinc-800 rounded-xl leading-relaxed focus:outline-none focus:ring-1 focus:ring-amber-400"></textarea>
                    </div>

                    <!-- Live Render Preview Paper Canvas (Flex-1 Scrollable) -->
                    <div class="flex-1 min-h-0 flex flex-col space-y-1">
                        <div class="flex items-center justify-between shrink-0">
                            <span class="text-3xs font-bold text-zinc-400 uppercase tracking-wider">Live Preview Rendering (True Canvas)</span>
                            <span id="customHtmlLivePreviewSubject" class="text-3xs font-bold text-zinc-600 dark:text-zinc-300 truncate max-w-xs"></span>
                        </div>
                        <div class="flex-1 min-h-0 overflow-y-auto p-4 bg-white text-zinc-900 rounded-xl border border-slate-200 shadow-xs">
                            <div id="customHtmlLivePreviewBody" class="text-xs text-zinc-900 leading-relaxed font-sans">
                                <p class="text-zinc-400 italic text-center py-8">Paste your HTML above to see the live rendering with real sample merge tags...</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer (Pinned) -->
            <div class="flex justify-end pt-2.5 border-t border-zinc-100 dark:border-zinc-800 shrink-0">
                <button type="button" onclick="closeTemplateLibrary()" class="px-5 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 rounded-xl hover:bg-slate-200 transition-colors cursor-pointer">
                    Close Library
                </button>
            </div>
        </div>
    </div>

    <!-- LIVE DESKTOP & MOBILE EMAIL PREVIEW MODAL -->
    <div id="previewModal" class="fixed inset-0 z-[999999] hidden bg-black/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6">
        <div class="bg-white dark:bg-[#141417] rounded-3xl max-w-3xl w-full h-[85vh] max-h-[85vh] flex flex-col p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-zinc-800 transform transition-all">
            
            <!-- Modal Header (Pinned) -->
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-sm border border-amber-200/60 dark:border-amber-500/30">
                        <i class="fas fa-magic"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Live Email Preview</h3>
                        <p class="text-3xs text-zinc-500">Rendered with sample lead: <strong class="text-zinc-800 dark:text-zinc-200">{{ $sampleContact->firstname ?? 'Vikram' }} ({{ $sampleContact->company ?? 'Razorpay' }})</strong></p>
                    </div>
                </div>

                <!-- Device Preview Switcher -->
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-[#09090B] p-1 rounded-xl border border-slate-200 dark:border-zinc-800">
                    <button type="button" id="btnPrevDesktop" onclick="setPreviewDevice('desktop')" class="px-3 py-1 bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 font-bold rounded-lg text-xs">
                        <i class="fas fa-desktop text-3xs mr-1"></i> Desktop
                    </button>
                    <button type="button" id="btnPrevMobile" onclick="setPreviewDevice('mobile')" class="px-3 py-1 bg-slate-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold rounded-lg text-xs">
                        <i class="fas fa-mobile-alt text-3xs mr-1"></i> Mobile
                    </button>
                </div>
            </div>

            <!-- Rendered Email Frame Container (Flex-1) -->
            <div class="flex-1 min-h-0 py-2 flex flex-col items-center justify-center overflow-hidden">
                <div id="previewDeviceFrame" class="w-full h-full flex flex-col min-h-0 bg-slate-100 dark:bg-[#09090B] p-4 rounded-2xl border border-slate-200 dark:border-zinc-800 space-y-3 font-sans transition-all">
                    
                    <!-- Mobile Top Speaker Notch Bar (Visible only in mobile mode) -->
                    <div id="mobilePreviewNotch" class="hidden w-24 h-3.5 bg-zinc-800 dark:bg-zinc-700 rounded-full mx-auto shrink-0 flex items-center justify-center space-x-1.5 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span>
                        <span class="w-8 h-1 rounded-full bg-zinc-600"></span>
                    </div>

                    <!-- Email Headers Box -->
                    <div class="bg-white dark:bg-[#141417] p-2.5 rounded-xl border border-slate-200 dark:border-zinc-800 space-y-1 text-xs shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold w-14 text-zinc-400 uppercase text-3xs tracking-wider">To:</span>
                            <span class="text-zinc-800 dark:text-zinc-200 font-mono text-xs truncate">{{ $sampleContact->email ?? 'vikram.aditya@razorpay.com' }}</span>
                        </div>
                        <div class="flex items-center gap-2 pt-1 border-t border-slate-100 dark:border-zinc-800">
                            <span class="font-bold w-14 text-zinc-400 uppercase text-3xs tracking-wider">Subject:</span>
                            <span id="previewModalSubject" class="text-zinc-900 dark:text-white font-bold text-xs truncate"></span>
                        </div>
                    </div>

                    <!-- True Email Client Paper Container (Scrolls Internally) -->
                    <div id="previewModalBodyContainer" class="flex-1 min-h-0 overflow-y-auto bg-white text-zinc-900 p-5 rounded-xl border border-slate-200 shadow-xs">
                        <div id="previewModalBody" class="text-sm text-zinc-900 leading-relaxed font-sans prose max-w-none"></div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer (Pinned) -->
            <div class="flex justify-end pt-2.5 border-t border-zinc-100 dark:border-zinc-800 shrink-0">
                <button type="button" onclick="closeLivePreview()" class="px-5 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 rounded-xl hover:bg-slate-200 transition-colors cursor-pointer">
                    Close Preview
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
