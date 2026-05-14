<?php
require_once __DIR__ . '/lang.php';

$settings = fetch_settings($pdo);
$about = $pdo->query('SELECT * FROM about ORDER BY id ASC LIMIT 1')->fetch() ?: [];
$resume = $pdo->query('SELECT * FROM resume ORDER BY id ASC LIMIT 1')->fetch() ?: [];

$skills = $pdo->query('SELECT * FROM resume_skills ORDER BY sort_order ASC, id ASC')->fetchAll();
$experience = $pdo->query('SELECT * FROM resume_experience ORDER BY sort_order ASC, id ASC')->fetchAll();
$education = $pdo->query('SELECT * FROM resume_education ORDER BY sort_order ASC, id ASC')->fetchAll();
$projects = $pdo->query('SELECT * FROM projects ORDER BY sort_order ASC, id DESC')->fetchAll();

$siteName = $settings['site_name'][$lang] ?? 'Portfolio';
$metaDescription = $settings['meta_description'][$lang] ?? '';
$heroGreeting = $settings['hero_greeting'][$lang] ?? $strings['hero_greeting'];
$heroTagline = $settings['hero_tagline'][$lang] ?? $strings['hero_tagline'];

$projectTags = [];
foreach ($projects as $project) {
    $tags = array_filter(array_map('trim', explode(',', $project['tags'] ?? '')));
    foreach ($tags as $tag) {
        $projectTags[$tag] = true;
    }
}
$projectTags = array_keys($projectTags);

$roles = [
    $strings['role_1'],
    $strings['role_2'],
    $strings['role_3'],
];
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>" dir="<?= e($dir) ?>" class="<?= $dir === 'rtl' ? 'rtl' : '' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($siteName) ?></title>
  <meta name="description" content="<?= e($metaDescription) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/devicons/1.8.0/css/devicons.min.css">
  <link rel="stylesheet" href="/portfolio/assets/css/custom.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            base: '#060a12',
            accent: '#22c55e',
            gold: '#f59e0b',
            soft: '#f1f5f9'
          }
        }
      }
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="text-soft">

  <!-- Scroll Progress -->
  <div id="scroll-progress" class="scroll-progress"></div>

  <!-- Cursor Glow -->
  <div id="cursor-glow" class="cursor-glow"></div>

  <!-- ======================== NAVIGATION ======================== -->
  <nav class="fixed top-0 w-full z-50 transition-all duration-300">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

      <!-- Logo -->
      <a href="#hero" class="font-semibold text-lg tracking-wide hover:text-accent transition-colors duration-200">
        <?= e($about['name_' . $lang] ?? 'Student Name') ?>
      </a>

      <!-- Desktop nav links -->
      <div class="hidden md:flex items-center gap-8">
        <a href="#about"    class="nav-link hover:text-accent transition-colors duration-200" data-section="about"><?= e($strings['nav_about']) ?></a>
        <a href="#resume"   class="nav-link hover:text-accent transition-colors duration-200" data-section="resume"><?= e($strings['nav_resume']) ?></a>
        <a href="#projects" class="nav-link hover:text-accent transition-colors duration-200" data-section="projects"><?= e($strings['nav_portfolio']) ?></a>
        <a href="#contact"  class="nav-link hover:text-accent transition-colors duration-200" data-section="contact"><?= e($strings['nav_contact']) ?></a>
      </div>

      <!-- Controls -->
      <div class="flex items-center gap-2">
        <a href="?lang=<?= $lang === 'en' ? 'ku' : 'en' ?>"
           class="px-3 py-1.5 rounded-full border border-white/15 hover:border-accent transition text-xs font-mono-alt">
          <?= $lang === 'en' ? '🏳️ کوردی' : '🇬🇧 EN' ?>
        </a>
        <button type="button" data-theme-toggle
                class="px-3 py-1.5 rounded-full border border-white/15 hover:border-accent transition text-xs">
          <span data-theme-label>🌙</span>
        </button>
        <button class="md:hidden p-2 rounded-lg border border-white/15 hover:border-accent transition" data-mobile-toggle aria-label="Menu">
          <span class="block w-5 h-0.5 bg-current mb-1 transition-all"></span>
          <span class="block w-5 h-0.5 bg-current mb-1 transition-all"></span>
          <span class="block w-5 h-0.5 bg-current transition-all"></span>
        </button>
      </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden hidden px-6 pb-4" data-mobile-menu>
      <div class="glass-card rounded-2xl p-4 flex flex-col gap-3 text-sm">
        <a href="#about"    class="nav-link hover:text-accent transition py-1" data-section="about"><?= e($strings['nav_about']) ?></a>
        <a href="#resume"   class="nav-link hover:text-accent transition py-1" data-section="resume"><?= e($strings['nav_resume']) ?></a>
        <a href="#projects" class="nav-link hover:text-accent transition py-1" data-section="projects"><?= e($strings['nav_portfolio']) ?></a>
        <a href="#contact"  class="nav-link hover:text-accent transition py-1" data-section="contact"><?= e($strings['nav_contact']) ?></a>
      </div>
    </div>
  </nav>

  <!-- ======================== HERO ======================== -->
  <section class="min-h-screen flex items-center hero-grid" id="hero">
    <div class="max-w-6xl mx-auto px-6 pt-32 pb-20 grid lg:grid-cols-2 gap-14 items-center">

      <!-- Left: intro text -->
      <div class="space-y-6 reveal">
        <div class="status-badge">
          <span class="dot"></span>
          <?= e($heroGreeting) ?>
        </div>

        <h1 class="text-4xl md:text-6xl font-bold leading-tight tracking-tight">
          <?= e($about['name_' . $lang] ?? 'Student Name') ?>
        </h1>

        <p class="text-xl font-mono-alt text-accent/90 min-h-[1.8rem]">
          <span data-typing data-roles='<?= e(json_encode($roles)) ?>'></span><span class="animate-pulse text-accent">|</span>
        </p>

        <p class="text-soft/65 max-w-lg leading-relaxed">
          <?= e($heroTagline) ?>
        </p>

        <div class="flex flex-wrap gap-4 pt-2">
          <a href="#projects" class="btn-primary">
            <?= e($strings['view_work']) ?> &rarr;
          </a>
          <?php if (!empty($resume['cv_file'])): ?>
            <a href="<?= e($resume['cv_file']) ?>" download class="btn-outline">
              ↓ <?= e($strings['download_cv']) ?>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Right: terminal card -->
      <div class="relative reveal stagger-2">
        <div class="glass-card rounded-3xl p-8">
          <!-- Mac-style traffic lights -->
          <div class="flex items-center gap-2 mb-5 pb-4 border-b border-white/10">
            <span class="w-3 h-3 rounded-full bg-red-400/80"></span>
            <span class="w-3 h-3 rounded-full bg-yellow-400/80"></span>
            <span class="w-3 h-3 rounded-full bg-green-400/80"></span>
            <span class="font-mono-alt text-soft/40 text-xs ml-2">~/portfolio — zsh</span>
          </div>
          <div class="space-y-3 text-sm font-mono-alt">
            <p><span class="text-gold">❯</span> <span class="text-accent">whoami</span></p>
            <p class="text-soft/60 pl-4"><?= e($about['name_' . $lang] ?? 'Student') ?> — <?= e($roles[0] ?? 'Developer') ?></p>
            <p class="mt-3"><span class="text-gold">❯</span> <span class="text-accent">skills</span> --top</p>
            <p class="text-soft/60 pl-4">PHP · MySQL · JavaScript · Tailwind · APIs</p>
            <p class="mt-3"><span class="text-gold">❯</span> <span class="text-accent">status</span></p>
            <p class="text-soft/60 pl-4"><span class="text-green-400">●</span> Building clean, bilingual experiences.</p>
            <p class="mt-3"><span class="text-gold">❯</span> <span class="text-accent/50">_</span><span class="animate-pulse text-accent/70">▋</span></p>
          </div>
        </div>
        <!-- Ambient glows -->
        <div class="absolute -bottom-10 -right-8 w-40 h-40 bg-gold/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -top-8 -left-6 w-32 h-32 bg-accent/10 blur-3xl pointer-events-none"></div>
      </div>

    </div>
  </section>

  <!-- ======================== ABOUT ======================== -->
  <section id="about" class="py-24">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-16 reveal">
        <span class="section-kicker">// <?= e($strings['about_title']) ?></span>
        <h2 class="section-title mt-2"><?= e($strings['about_title']) ?></h2>
        <div class="section-underline mx-auto mt-4"></div>
      </div>

      <div class="grid lg:grid-cols-2 gap-14 items-center">

        <!-- Profile image with spinning ring -->
        <div class="flex justify-center reveal stagger-1">
          <div class="profile-ring-wrap">
            <img src="<?= e($about['profile_image'] ?? '/portfolio/uploads/profile/default.svg') ?>"
                 alt="Profile"
                 class="w-60 h-60 object-cover rounded-full neon-ring">
          </div>
        </div>

        <!-- Bio + info -->
        <div class="space-y-6 reveal stagger-2">
          <p class="text-soft/75 leading-relaxed text-base">
            <?= e($about['bio_' . $lang] ?? 'Short bio goes here.') ?>
          </p>

          <!-- Stats row -->
          <div class="grid grid-cols-3 gap-3">
            <div class="stat-card reveal stagger-1">
              <div class="stat-number" data-counter="<?= count($projects) ?>" data-suffix="+"><?= count($projects) ?>+</div>
              <p class="text-soft/55 text-xs mt-1"><?= e($strings['nav_portfolio']) ?></p>
            </div>
            <div class="stat-card reveal stagger-2">
              <div class="stat-number" data-counter="<?= count($skills) ?>" data-suffix="+"><?= count($skills) ?>+</div>
              <p class="text-soft/55 text-xs mt-1"><?= e($strings['skills_title']) ?></p>
            </div>
            <div class="stat-card reveal stagger-3">
              <div class="stat-number"><?= e($about['graduation_year'] ?? '2026') ?></div>
              <p class="text-soft/55 text-xs mt-1">Graduation</p>
            </div>
          </div>

          <!-- Info cards with icons -->
          <div class="grid sm:grid-cols-2 gap-3">
            <div class="glass-card rounded-xl p-4 flex items-start gap-3">
              <span class="text-xl mt-0.5">🎓</span>
              <div>
                <p class="text-soft/50 text-xs">University</p>
                <p class="text-white font-semibold text-sm"><?= e($about['university_' . $lang] ?? 'University Name') ?></p>
              </div>
            </div>
            <div class="glass-card rounded-xl p-4 flex items-start gap-3">
              <span class="text-xl mt-0.5">📅</span>
              <div>
                <p class="text-soft/50 text-xs">Graduation</p>
                <p class="text-white font-semibold text-sm"><?= e($about['graduation_year'] ?? '2026') ?></p>
              </div>
            </div>
            <div class="glass-card rounded-xl p-4 flex items-start gap-3">
              <span class="text-xl mt-0.5">✉️</span>
              <div>
                <p class="text-soft/50 text-xs">Email</p>
                <p class="text-white font-semibold text-sm break-all"><?= e($about['email'] ?? 'email@example.com') ?></p>
              </div>
            </div>
            <div class="glass-card rounded-xl p-4 flex items-start gap-3">
              <span class="text-xl mt-0.5">📱</span>
              <div>
                <p class="text-soft/50 text-xs">Phone</p>
                <p class="text-white font-semibold text-sm"><?= e($about['phone'] ?? '+964 000 0000') ?></p>
              </div>
            </div>
          </div>

          <!-- Social buttons -->
          <div class="flex flex-wrap gap-3">
            <?php if (!empty($about['github_url'])): ?>
              <a href="<?= e($about['github_url']) ?>" class="social-btn" target="_blank" rel="noopener noreferrer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                GitHub
              </a>
            <?php endif; ?>
            <?php if (!empty($about['linkedin_url'])): ?>
              <a href="<?= e($about['linkedin_url']) ?>" class="social-btn" target="_blank" rel="noopener noreferrer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                LinkedIn
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ======================== RESUME ======================== -->
  <section id="resume" class="py-24 section-panel">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-16 reveal">
        <span class="section-kicker">// <?= e($strings['resume_title']) ?></span>
        <h2 class="section-title mt-2"><?= e($strings['resume_title']) ?></h2>
        <div class="section-underline mx-auto mt-4"></div>
      </div>

      <div class="grid lg:grid-cols-3 gap-8">

        <!-- Skills panel -->
        <div class="lg:col-span-1 glass-card rounded-2xl p-6 reveal stagger-1">
          <h3 class="text-lg font-semibold mb-5 text-white flex items-center gap-2">
            <span class="text-accent">⚡</span> <?= e($strings['skills_title']) ?>
          </h3>
          <?php foreach ($skills as $i => $skill): ?>
            <div class="mb-5 reveal stagger-<?= min($i + 1, 6) ?>">
              <div class="flex justify-between items-center mb-1.5">
                <span class="text-white text-sm font-medium"><?= e($skill['skill_name_' . $lang]) ?></span>
                <span class="skill-level-badge"><?= e((string)$skill['skill_level']) ?>%</span>
              </div>
              <div class="skill-bar-track">
                <div class="skill-bar" data-level="<?= e((string)$skill['skill_level']) ?>" style="width:0%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Experience + Education -->
        <div class="lg:col-span-2 space-y-12">

          <!-- Experience -->
          <div class="reveal">
            <h3 class="text-lg font-semibold mb-6 text-white flex items-center gap-2">
              <span class="text-gold">💼</span> <?= e($strings['experience_title']) ?>
            </h3>
            <div class="timeline space-y-10">
              <?php foreach ($experience as $index => $item): ?>
                <div class="relative reveal stagger-<?= min($index + 1, 6) ?>">
                  <span class="timeline-dot" style="top:22px;"></span>
                  <div class="timeline-card glass-card rounded-2xl p-6 ml-auto <?= $index % 2 === 0 ? '' : 'mr-auto' ?>">
                    <h4 class="text-base font-semibold text-white"><?= e($item['title_' . $lang]) ?></h4>
                    <p class="text-gold text-xs mt-1 font-mono-alt"><?= e($item['company_' . $lang]) ?> · <?= e($item['date_range_' . $lang]) ?></p>
                    <p class="text-soft/65 mt-2 text-sm leading-relaxed"><?= e($item['description_' . $lang]) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Education -->
          <div class="reveal">
            <h3 class="text-lg font-semibold mb-6 text-white flex items-center gap-2">
              <span class="text-purple-400">🎓</span> <?= e($strings['education_title']) ?>
            </h3>
            <div class="space-y-4">
              <?php foreach ($education as $i => $item): ?>
                <div class="glass-card rounded-2xl p-6 reveal stagger-<?= min($i + 1, 6) ?>">
                  <h4 class="text-base font-semibold text-white"><?= e($item['degree_' . $lang]) ?></h4>
                  <p class="text-gold text-xs mt-1 font-mono-alt"><?= e($item['institution_' . $lang]) ?> · <?= e($item['year_range']) ?></p>
                  <p class="text-soft/65 mt-2 text-sm leading-relaxed"><?= e($item['description_' . $lang]) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <?php if (!empty($resume['cv_file'])): ?>
            <div class="reveal">
              <a href="<?= e($resume['cv_file']) ?>" download class="btn-primary inline-flex">
                ↓ <?= e($strings['download_cv']) ?>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ======================== PROJECTS ======================== -->
  <section id="projects" class="py-24">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-12 reveal">
        <span class="section-kicker">// <?= e($strings['portfolio_title']) ?></span>
        <h2 class="section-title mt-2"><?= e($strings['portfolio_title']) ?></h2>
        <div class="section-underline mx-auto mt-4"></div>
      </div>

      <!-- Filter pills -->
      <div class="flex flex-wrap gap-3 justify-center mb-10 reveal">
        <button class="filter-pill is-active" data-filter="all"><?= e($strings['filters_all']) ?></button>
        <button class="filter-pill" data-filter="featured"><?= e($strings['filters_featured']) ?></button>
        <?php foreach ($projectTags as $tag): ?>
          <button class="filter-pill" data-filter="<?= e($tag) ?>"><?= e($tag) ?></button>
        <?php endforeach; ?>
      </div>

      <!-- Project grid -->
      <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($projects as $i => $project): ?>
          <?php $tags = array_filter(array_map('trim', explode(',', $project['tags'] ?? ''))); ?>
          <div class="project-card glass-card rounded-2xl overflow-hidden reveal stagger-<?= min(($i % 3) + 1, 6) ?>"
               data-tags="<?= e(implode(',', $tags)) ?>"
               data-featured="<?= e((string)$project['is_featured']) ?>">

            <!-- Image with overlay gradient -->
            <div class="project-img-wrap">
              <img src="<?= e($project['thumbnail'] ?: '/portfolio/uploads/projects/default.svg') ?>"
                   alt="<?= e($project['title_' . $lang]) ?>">
            </div>

            <!-- Card body -->
            <div class="p-6 space-y-3">
              <div class="flex items-start justify-between gap-2">
                <h3 class="text-base font-semibold text-white leading-snug"><?= e($project['title_' . $lang]) ?></h3>
                <?php if ((int)$project['is_featured'] === 1): ?>
                  <span class="flex-shrink-0 text-xs px-2 py-0.5 bg-gold text-black rounded-full font-semibold">★</span>
                <?php endif; ?>
              </div>
              <p class="text-soft/60 text-sm leading-relaxed"><?= e($project['description_' . $lang]) ?></p>
              <div class="flex flex-wrap gap-1.5">
                <?php foreach ($tags as $tag): ?>
                  <span class="text-xs px-2.5 py-0.5 bg-white/10 border border-white/10 rounded-full text-soft/60"><?= e($tag) ?></span>
                <?php endforeach; ?>
              </div>
              <div class="flex items-center gap-4 pt-1">
                <?php if (!empty($project['demo_url'])): ?>
                  <a href="<?= e($project['demo_url']) ?>" target="_blank" rel="noopener noreferrer"
                     class="text-gold hover:text-white transition text-sm font-medium flex items-center gap-1">
                    ↗ Live Demo
                  </a>
                <?php endif; ?>
                <?php if (!empty($project['github_url'])): ?>
                  <a href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener noreferrer"
                     class="text-soft/55 hover:text-white transition text-sm font-medium flex items-center gap-1">
                    ⎇ GitHub
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ======================== CONTACT ======================== -->
  <section id="contact" class="py-24 section-panel">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-12 reveal">
        <span class="section-kicker">// <?= e($strings['contact_title']) ?></span>
        <h2 class="section-title mt-2"><?= e($strings['contact_title']) ?></h2>
        <div class="section-underline mx-auto mt-4"></div>
      </div>

      <div class="grid lg:grid-cols-2 gap-10 items-start">

        <!-- Contact info -->
        <div class="space-y-6 reveal stagger-1">
          <h3 class="text-xl font-semibold text-white"><?= e($strings['contact_info_title']) ?></h3>
          <p class="text-soft/65 leading-relaxed text-sm"><?= e($about['bio_' . $lang] ?? '') ?></p>

          <div class="space-y-3">
            <div class="glass-card rounded-xl p-4 flex items-center gap-4">
              <div class="w-10 h-10 rounded-lg bg-accent/10 border border-accent/20 flex items-center justify-center flex-shrink-0">
                <span class="text-lg">✉️</span>
              </div>
              <div>
                <p class="text-soft/45 text-xs">Email</p>
                <p class="text-white font-medium text-sm"><?= e($about['email'] ?? 'email@example.com') ?></p>
              </div>
            </div>
            <div class="glass-card rounded-xl p-4 flex items-center gap-4">
              <div class="w-10 h-10 rounded-lg bg-gold/10 border border-gold/20 flex items-center justify-center flex-shrink-0">
                <span class="text-lg">📱</span>
              </div>
              <div>
                <p class="text-soft/45 text-xs">Phone</p>
                <p class="text-white font-medium text-sm"><?= e($about['phone'] ?? '+964 000 0000') ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact form with floating labels -->
        <form class="glass-card rounded-2xl p-8 space-y-5 reveal stagger-2" data-contact-form>
          <div class="grid md:grid-cols-2 gap-4">
            <div class="field-wrap">
              <input type="text" name="name" placeholder="Name" class="field">
              <label><?= e($strings['contact_name']) ?></label>
            </div>
            <div class="field-wrap">
              <input type="email" name="email" placeholder="Email" class="field">
              <label><?= e($strings['contact_email']) ?></label>
            </div>
          </div>
          <div class="field-wrap">
            <input type="text" name="subject" placeholder="Subject" class="field">
            <label><?= e($strings['contact_subject']) ?></label>
          </div>
          <div class="field-wrap is-textarea">
            <textarea name="message" rows="5" placeholder="Message" class="field"></textarea>
            <label><?= e($strings['contact_message']) ?></label>
          </div>
          <button type="submit" class="btn-primary w-full justify-center">
            <?= e($strings['send_message']) ?> →
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- ======================== FOOTER ======================== -->
  <footer class="py-14 text-center relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none"
         style="background: radial-gradient(600px circle at 50% 100%, rgba(34,197,94,0.06) 0%, transparent 60%);"></div>
    <div class="relative z-10">
      <p class="font-mono-alt text-soft/40 text-sm">
        © <span id="year"></span>
        <span class="text-accent/70"><?= e($about['name_' . $lang] ?? 'Student Name') ?></span>
        · <?= e($strings['footer_rights']) ?>
      </p>
      <button class="mt-4 text-xs text-soft/35 hover:text-accent transition-colors duration-200 font-mono-alt"
              onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
        ↑ <?= e($strings['back_to_top']) ?>
      </button>
    </div>
  </footer>

  <script src="/portfolio/assets/js/main.js"></script>
  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
