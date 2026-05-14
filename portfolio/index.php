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
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            base: '#0a0f1e',
            accent: '#00d4aa',
            soft: '#f0f4ff'
          }
        }
      }
    };
  </script>
</head>
<body class="text-soft">
  <nav class="fixed top-0 w-full z-50 transition-all">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="font-semibold text-xl tracking-wide">
        <?= e($about['name_' . $lang] ?? 'Student Name') ?>
      </div>
      <div class="hidden md:flex items-center gap-6">
        <a href="#about" class="hover:text-accent transition"><?= e($strings['nav_about']) ?></a>
        <a href="#resume" class="hover:text-accent transition"><?= e($strings['nav_resume']) ?></a>
        <a href="#projects" class="hover:text-accent transition"><?= e($strings['nav_portfolio']) ?></a>
        <a href="#contact" class="hover:text-accent transition"><?= e($strings['nav_contact']) ?></a>
      </div>
      <div class="flex items-center gap-3">
        <a href="?lang=<?= $lang === 'en' ? 'ku' : 'en' ?>"
           class="px-4 py-2 rounded-full border border-white/20 hover:border-accent transition text-sm">
          <?= $lang === 'en' ? '🏳️ کوردی' : '🇬🇧 EN' ?>
        </a>
        <button type="button" data-theme-toggle class="px-4 py-2 rounded-full border border-white/20 hover:border-accent transition text-sm">
          <span data-theme-label>🌙</span>
        </button>
        <button class="md:hidden" data-mobile-toggle aria-label="Menu">
          <span class="block w-6 h-0.5 bg-white mb-1"></span>
          <span class="block w-6 h-0.5 bg-white mb-1"></span>
          <span class="block w-6 h-0.5 bg-white"></span>
        </button>
      </div>
    </div>
    <div class="md:hidden hidden px-6 pb-4" data-mobile-menu>
      <div class="glass-card rounded-2xl p-4 flex flex-col gap-3">
        <a href="#about" class="hover:text-accent transition"><?= e($strings['nav_about']) ?></a>
        <a href="#resume" class="hover:text-accent transition"><?= e($strings['nav_resume']) ?></a>
        <a href="#projects" class="hover:text-accent transition"><?= e($strings['nav_portfolio']) ?></a>
        <a href="#contact" class="hover:text-accent transition"><?= e($strings['nav_contact']) ?></a>
      </div>
    </div>
  </nav>

  <section class="min-h-screen flex items-center hero-grid" id="hero">
    <div class="max-w-6xl mx-auto px-6 pt-32 pb-20 grid lg:grid-cols-2 gap-12 items-center">
      <div class="space-y-6">
        <span class="font-mono-alt text-accent tracking-widest uppercase text-sm">// <?= e($heroGreeting) ?></span>
        <h1 class="text-4xl md:text-6xl font-bold leading-tight">
          <?= e($about['name_' . $lang] ?? 'Student Name') ?>
        </h1>
        <p class="text-xl text-soft/80">
          <span data-typing data-roles='<?= e(json_encode($roles)) ?>'></span>
        </p>
        <p class="text-soft/70 max-w-xl">
          <?= e($heroTagline) ?>
        </p>
        <div class="flex flex-wrap gap-4">
          <a href="#projects" class="px-6 py-3 bg-accent text-black font-semibold rounded-lg shadow-lg shadow-accent/30 hover:-translate-y-0.5 transition">
            <?= e($strings['view_work']) ?>
          </a>
          <?php if (!empty($resume['cv_file'])): ?>
            <a href="<?= e($resume['cv_file']) ?>" download class="px-6 py-3 border-2 border-accent text-accent font-semibold rounded-lg hover:bg-accent hover:text-black transition">
              <?= e($strings['download_cv']) ?>
            </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="relative">
        <div class="glass-card rounded-3xl p-8 reveal">
          <div class="flex items-center justify-between mb-6">
            <span class="font-mono-alt text-accent">terminal</span>
            <span class="text-soft/60">~/portfolio</span>
          </div>
          <div class="space-y-3 text-sm text-soft/80">
            <p><span class="text-accent">$</span> skills --list</p>
            <p class="text-soft/60">PHP · MySQL · JavaScript · Tailwind · APIs</p>
            <p><span class="text-accent">$</span> status</p>
            <p class="text-soft/60">Building clean, bilingual experiences.</p>
          </div>
        </div>
        <div class="absolute -bottom-10 -right-6 w-32 h-32 bg-accent/20 blur-2xl"></div>
      </div>
    </div>
  </section>

  <section id="about" class="py-24">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-16">
        <span class="text-accent text-sm font-mono-alt tracking-widest uppercase">// <?= e($strings['about_title']) ?></span>
        <h2 class="text-4xl font-bold text-white mt-2"><?= e($strings['about_title']) ?></h2>
        <div class="w-16 h-1 bg-accent mx-auto mt-4 rounded-full"></div>
      </div>
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="flex justify-center">
          <div class="relative">
            <img src="<?= e($about['profile_image'] ?? '/portfolio/uploads/profile/default.svg') ?>" alt="Profile" class="w-64 h-64 object-cover rounded-full border-4 border-accent neon-ring">
          </div>
        </div>
        <div class="space-y-6 reveal">
          <p class="text-soft/80 leading-relaxed">
            <?= e($about['bio_' . $lang] ?? 'Short bio goes here.') ?>
          </p>
          <div class="grid sm:grid-cols-2 gap-4">
            <div class="glass-card rounded-2xl p-4">
              <p class="text-soft/60 text-sm">University</p>
              <p class="text-white font-semibold"><?= e($about['university_' . $lang] ?? 'University Name') ?></p>
            </div>
            <div class="glass-card rounded-2xl p-4">
              <p class="text-soft/60 text-sm">Graduation</p>
              <p class="text-white font-semibold"><?= e($about['graduation_year'] ?? '2026') ?></p>
            </div>
            <div class="glass-card rounded-2xl p-4">
              <p class="text-soft/60 text-sm">Email</p>
              <p class="text-white font-semibold"><?= e($about['email'] ?? 'email@example.com') ?></p>
            </div>
            <div class="glass-card rounded-2xl p-4">
              <p class="text-soft/60 text-sm">Phone</p>
              <p class="text-white font-semibold"><?= e($about['phone'] ?? '+964 000 0000') ?></p>
            </div>
          </div>
          <div class="flex gap-4">
            <?php if (!empty($about['github_url'])): ?>
              <a href="<?= e($about['github_url']) ?>" class="text-accent hover:text-white transition" target="_blank" rel="noopener noreferrer">GitHub</a>
            <?php endif; ?>
            <?php if (!empty($about['linkedin_url'])): ?>
              <a href="<?= e($about['linkedin_url']) ?>" class="text-accent hover:text-white transition" target="_blank" rel="noopener noreferrer">LinkedIn</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="resume" class="py-24 bg-black/20">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-16">
        <span class="text-accent text-sm font-mono-alt tracking-widest uppercase">// <?= e($strings['resume_title']) ?></span>
        <h2 class="text-4xl font-bold text-white mt-2"><?= e($strings['resume_title']) ?></h2>
        <div class="w-16 h-1 bg-accent mx-auto mt-4 rounded-full"></div>
      </div>

      <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 glass-card rounded-2xl p-6 reveal">
          <h3 class="text-xl font-semibold mb-4 text-white"><?= e($strings['skills_title']) ?></h3>
          <?php foreach ($skills as $skill): ?>
            <div class="mb-4">
              <div class="flex justify-between mb-1 text-sm">
                <span class="text-white font-medium"><?= e($skill['skill_name_' . $lang]) ?></span>
                <span class="text-accent"><?= e((string) $skill['skill_level']) ?>%</span>
              </div>
              <div class="w-full bg-white/10 rounded-full h-2">
                <div class="h-2 bg-gradient-to-r from-accent to-cyan-400 rounded-full skill-bar" data-level="<?= e((string) $skill['skill_level']) ?>" style="width:0%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="lg:col-span-2 space-y-10">
          <div class="reveal">
            <h3 class="text-xl font-semibold mb-6 text-white"><?= e($strings['experience_title']) ?></h3>
            <div class="timeline space-y-10">
              <?php foreach ($experience as $index => $item): ?>
                <div class="relative">
                  <span class="timeline-dot" style="top: 22px;"></span>
                  <div class="timeline-card glass-card rounded-2xl p-6 ml-auto <?= $index % 2 === 0 ? '' : 'mr-auto' ?>">
                    <h4 class="text-lg font-semibold text-white"><?= e($item['title_' . $lang]) ?></h4>
                    <p class="text-accent text-sm"><?= e($item['company_' . $lang]) ?> · <?= e($item['date_range_' . $lang]) ?></p>
                    <p class="text-soft/70 mt-2"><?= e($item['description_' . $lang]) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="reveal">
            <h3 class="text-xl font-semibold mb-6 text-white"><?= e($strings['education_title']) ?></h3>
            <div class="space-y-6">
              <?php foreach ($education as $item): ?>
                <div class="glass-card rounded-2xl p-6">
                  <h4 class="text-lg font-semibold text-white"><?= e($item['degree_' . $lang]) ?></h4>
                  <p class="text-accent text-sm"><?= e($item['institution_' . $lang]) ?> · <?= e($item['year_range']) ?></p>
                  <p class="text-soft/70 mt-2"><?= e($item['description_' . $lang]) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <?php if (!empty($resume['cv_file'])): ?>
            <div class="reveal">
              <a href="<?= e($resume['cv_file']) ?>" download class="inline-flex items-center gap-2 px-6 py-3 bg-accent text-black font-semibold rounded-lg shadow-lg shadow-accent/30 hover:-translate-y-0.5 transition">
                <?= e($strings['download_cv']) ?>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <section id="projects" class="py-24">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-12">
        <span class="text-accent text-sm font-mono-alt tracking-widest uppercase">// <?= e($strings['portfolio_title']) ?></span>
        <h2 class="text-4xl font-bold text-white mt-2"><?= e($strings['portfolio_title']) ?></h2>
        <div class="w-16 h-1 bg-accent mx-auto mt-4 rounded-full"></div>
      </div>

      <div class="flex flex-wrap gap-3 justify-center mb-10">
        <button class="px-4 py-2 rounded-full border border-white/20 text-sm hover:border-accent transition bg-teal-500 text-black" data-filter="all"><?= e($strings['filters_all']) ?></button>
        <button class="px-4 py-2 rounded-full border border-white/20 text-sm hover:border-accent transition" data-filter="featured"><?= e($strings['filters_featured']) ?></button>
        <?php foreach ($projectTags as $tag): ?>
          <button class="px-4 py-2 rounded-full border border-white/20 text-sm hover:border-accent transition" data-filter="<?= e($tag) ?>"><?= e($tag) ?></button>
        <?php endforeach; ?>
      </div>

      <div class="grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($projects as $project): ?>
          <?php $tags = array_filter(array_map('trim', explode(',', $project['tags'] ?? ''))); ?>
          <div class="project-card glass-card rounded-2xl overflow-hidden reveal" data-tags="<?= e(implode(',', $tags)) ?>" data-featured="<?= e((string) $project['is_featured']) ?>">
            <div class="h-48 overflow-hidden">
              <img src="<?= e($project['thumbnail'] ?: '/portfolio/uploads/projects/default.svg') ?>" alt="Project" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white"><?= e($project['title_' . $lang]) ?></h3>
                <?php if ((int) $project['is_featured'] === 1): ?>
                  <span class="text-xs px-2 py-1 bg-accent text-black rounded-full">Featured</span>
                <?php endif; ?>
              </div>
              <p class="text-soft/70 text-sm"><?= e($project['description_' . $lang]) ?></p>
              <div class="flex flex-wrap gap-2">
                <?php foreach ($tags as $tag): ?>
                  <span class="text-xs px-2 py-1 bg-white/10 rounded-full"><?= e($tag) ?></span>
                <?php endforeach; ?>
              </div>
              <div class="flex items-center gap-3">
                <?php if (!empty($project['demo_url'])): ?>
                  <a href="<?= e($project['demo_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-accent hover:text-white transition">Live Demo</a>
                <?php endif; ?>
                <?php if (!empty($project['github_url'])): ?>
                  <a href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-accent hover:text-white transition">GitHub</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="contact" class="py-24 bg-black/20">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-12">
        <span class="text-accent text-sm font-mono-alt tracking-widest uppercase">// <?= e($strings['contact_title']) ?></span>
        <h2 class="text-4xl font-bold text-white mt-2"><?= e($strings['contact_title']) ?></h2>
        <div class="w-16 h-1 bg-accent mx-auto mt-4 rounded-full"></div>
      </div>

      <div class="grid lg:grid-cols-2 gap-10">
        <div class="space-y-6 reveal">
          <h3 class="text-2xl font-semibold text-white"><?= e($strings['contact_info_title']) ?></h3>
          <p class="text-soft/70"><?= e($about['bio_' . $lang] ?? '') ?></p>
          <div class="glass-card rounded-2xl p-6 space-y-3">
            <p class="text-soft/70"><?= e($about['email'] ?? 'email@example.com') ?></p>
            <p class="text-soft/70"><?= e($about['phone'] ?? '+964 000 0000') ?></p>
          </div>
        </div>

        <form class="glass-card rounded-2xl p-8 space-y-4 reveal" data-contact-form>
          <div class="grid md:grid-cols-2 gap-4">
            <input type="text" name="name" placeholder="<?= e($strings['contact_name']) ?>" class="w-full rounded-lg bg-white/10 border border-white/10 px-4 py-3 text-white">
            <input type="email" name="email" placeholder="<?= e($strings['contact_email']) ?>" class="w-full rounded-lg bg-white/10 border border-white/10 px-4 py-3 text-white">
          </div>
          <input type="text" name="subject" placeholder="<?= e($strings['contact_subject']) ?>" class="w-full rounded-lg bg-white/10 border border-white/10 px-4 py-3 text-white">
          <textarea name="message" rows="5" placeholder="<?= e($strings['contact_message']) ?>" class="w-full rounded-lg bg-white/10 border border-white/10 px-4 py-3 text-white"></textarea>
          <button type="submit" class="px-6 py-3 bg-accent text-black font-semibold rounded-lg shadow-lg shadow-accent/30 hover:-translate-y-0.5 transition">
            <?= e($strings['send_message']) ?>
          </button>
        </form>
      </div>
    </div>
  </section>

  <footer class="py-12 text-center">
    <p class="text-soft/60">© <span id="year"></span> <?= e($about['name_' . $lang] ?? 'Student Name') ?>. <?= e($strings['footer_rights']) ?></p>
    <button class="mt-4 text-accent hover:text-white transition" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
      <?= e($strings['back_to_top']) ?>
    </button>
  </footer>

  <script src="/portfolio/assets/js/main.js"></script>
  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
