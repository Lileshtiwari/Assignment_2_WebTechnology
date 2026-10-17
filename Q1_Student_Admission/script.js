/* STUDENT REGISTRATION FORM VALIDATION */

'use strict';

/* ---- SECTION NAVIGATION ---- */

const sections  = ['personal','contact','academic','course','account','documents','declaration'];
let currentIdx  = 0;

function showSection(idx) {
  sections.forEach((id, i) => {
    const el  = document.getElementById('section-' + id);
    const btn = document.querySelector('[data-section="' + id + '"]');
    if (el)  el.classList.toggle('active', i === idx);
    if (btn) {
      btn.classList.toggle('active', i === idx);
      if (i < idx) btn.classList.add('done');
    }
  });
  currentIdx = idx;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function nextSection() { showSection(currentIdx + 1); }
function prevSection() { showSection(currentIdx - 1); }

/* ---- FIELD VALIDATORS ---- */

/* Returns true when the field is valid */
const validators = {

  firstName(val)          { return val.trim().length >= 2; },
  lastName(val)           { return val.trim().length >= 2; },
  rollNumber(val)         { return /^[A-Za-z0-9]{4,20}$/.test(val.trim()); },
  dob(val)                { return val !== ''; },
  gender(val)             { return val !== ''; },
  nationality(val)        { return val.trim().length >= 2; },

  email(val)              { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim()); },
  phone(val)              { return /^[6-9][0-9]{9}$/.test(val.trim()); },
  address(val)            { return val.trim().length >= 10; },
  city(val)               { return val.trim().length >= 2; },
  pincode(val)            { return /^[0-9]{6}$/.test(val.trim()); },

  sslcSchool(val)         { return val.trim().length >= 3; },
  sslcYear(val)           { const y = parseInt(val); return y >= 2000 && y <= 2030; },
  sslcPercent(val)        { const p = parseFloat(val); return p >= 0 && p <= 100; },
  hscSchool(val)          { return val.trim().length >= 3; },
  hscYear(val)            { const y = parseInt(val); return y >= 2000 && y <= 2030; },
  hscPercent(val)         { const p = parseFloat(val); return p >= 0 && p <= 100; },
  hscStream(val)          { return val !== ''; },

  course(val)             { return val !== ''; },
  admissionCategory(val)  { return val !== ''; },

  password(val)           { return passwordStrengthScore(val) >= 4; },
  confirmPassword(val)    {
    return val === document.getElementById('password').value && val !== '';
  },

  profilePhoto(input)     {
    return validateFile(input, ['jpg','jpeg','png'], 2);
  },
  idProof(input)          {
    return validateFile(input, ['pdf','jpg','jpeg','png'], 2);
  },
  declaration(el)         {
    if (typeof el === 'object' && el !== null && 'checked' in el) {
      return el.checked;
    }
    const chk = document.getElementById('declaration');
    return chk ? chk.checked : false;
  },
  otherNationality(val)   {
    const sel = document.getElementById('nationalitySelect');
    if (sel && sel.value === 'Other') {
      return typeof val === 'string' && val.trim().length >= 2;
    }
    return true;
  },
};

/* ---- HELPER: FILE VALIDATION ---- */

function validateFile(input, allowedExts, maxMB) {
  if (!input.files || input.files.length === 0) return false;
  const file = input.files[0];
  const ext  = file.name.split('.').pop().toLowerCase();
  if (!allowedExts.includes(ext)) return false;
  if (file.size > maxMB * 1024 * 1024) return false;
  return true;
}

/* ---- HELPER: SHOW / CLEAR FIELD ERROR ---- */

function markField(input, valid) {
  input.classList.toggle('error',   !valid);
  input.classList.toggle('success',  valid);
  const err = document.getElementById('err-' + input.id);
  if (err) err.classList.toggle('visible', !valid);
}

function validateField(input) {
  const fn    = validators[input.id];
  if (!fn) return true;  /* no rule — skip */
  const val   = (input.type === 'file' || input.type === 'checkbox') ? input : input.value;
  const valid = fn(val);
  markField(input, valid);
  return valid;
}

/* ---- REAL-TIME VALIDATION ---- */

document.querySelectorAll('.form-control').forEach(el => {
  el.addEventListener('blur',  () => validateField(el));
  el.addEventListener('input', () => {
    if (el.classList.contains('error')) validateField(el);
  });
  el.addEventListener('change', () => validateField(el));
});

const declBox = document.getElementById('declaration');
if (declBox) {
  declBox.addEventListener('change', () => validateField(declBox));
}

/* ---- PASSWORD STRENGTH ---- */

function passwordStrengthScore(val) {
  let score = 0;
  if (val.length >= 8)                 score++;
  if (/[A-Z]/.test(val))              score++;
  if (/[a-z]/.test(val))              score++;
  if (/[0-9]/.test(val))              score++;
  if (/[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(val)) score++;
  return score;
}

document.getElementById('password').addEventListener('input', function() {
  const val   = this.value;
  const score = passwordStrengthScore(val);
  const fill  = document.getElementById('strengthFill');
  const label = document.getElementById('strengthLabel');

  /* Update strength bar */
  fill.className  = 'strength-fill';
  label.className = 'strength-label';

  if (val === '') {
    fill.style.width = '0';
    label.textContent = 'Enter a password';
  } else if (score <= 1) {
    fill.classList.add('weak');   label.classList.add('weak');   label.textContent = 'Weak';
  } else if (score === 2) {
    fill.classList.add('fair');   label.classList.add('fair');   label.textContent = 'Fair';
  } else if (score === 3) {
    fill.classList.add('good');   label.classList.add('good');   label.textContent = 'Good';
  } else if (score >= 4) {
    fill.classList.add('strong'); label.classList.add('strong'); label.textContent = 'Strong';
  }

  /* Update per-hint styling */
  toggle('hint-length',  val.length >= 8);
  toggle('hint-upper',   /[A-Z]/.test(val));
  toggle('hint-lower',   /[a-z]/.test(val));
  toggle('hint-number',  /[0-9]/.test(val));
  toggle('hint-special', /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(val));
});

function toggle(id, met) {
  document.getElementById(id).classList.toggle('met', met);
}

/* ---- PASSWORD TOGGLE (show/hide) ---- */

function bindPasswordToggle(btnId, inputId) {
  const btn   = document.getElementById(btnId);
  const input = document.getElementById(inputId);
  if (!btn || !input) return;
  btn.addEventListener('click', () => {
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.querySelector('.icon-eye').style.display     = show ? 'none'  : '';
    btn.querySelector('.icon-eye-off').style.display = show ? ''     : 'none';
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
  });
}

bindPasswordToggle('togglePassword',        'password');
bindPasswordToggle('toggleConfirmPassword', 'confirmPassword');

/* ---- FILE UPLOAD PREVIEW ---- */

function bindFileInput(inputId, dropzoneId, previewId) {
  const input    = document.getElementById(inputId);
  const dropzone = document.getElementById(dropzoneId);
  const preview  = document.getElementById(previewId);
  if (!input || !dropzone || !preview) return;

  function handleFile(file) {
    const ext      = file.name.split('.').pop().toLowerCase();
    const allowed  = ['jpg','jpeg','png','pdf'];
    const maxBytes = 2 * 1024 * 1024;

    if (!allowed.includes(ext) || file.size > maxBytes) {
      dropzone.classList.remove('has-file');
      dropzone.classList.add('error-state');
      preview.hidden = true;
      validateField(input);
      return;
    }

    dropzone.classList.add('has-file');
    dropzone.classList.remove('error-state');
    markField(input, true);

    const sizeStr = file.size < 1024
      ? file.size + ' B'
      : file.size < 1024 * 1024
        ? (file.size / 1024).toFixed(1) + ' KB'
        : (file.size / (1024 * 1024)).toFixed(1) + ' MB';

    preview.hidden = false;
    preview.innerHTML = `
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      <div>
        <div class="file-name">${escapeHTML(file.name)}</div>
        <div class="file-size">${sizeStr}</div>
      </div>
      <button type="button" class="remove-file" aria-label="Remove file">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    `;

    preview.querySelector('.remove-file').addEventListener('click', () => {
      input.value    = '';
      preview.hidden = true;
      dropzone.classList.remove('has-file');
      markField(input, false);
    });
  }

  input.addEventListener('change', () => {
    if (input.files[0]) handleFile(input.files[0]);
  });

  /* Drag-and-drop */
  dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragover'); });
  dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
  dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('dragover');
    if (e.dataTransfer.files[0]) {
      const dt = new DataTransfer();
      dt.items.add(e.dataTransfer.files[0]);
      input.files = dt.files;
      handleFile(e.dataTransfer.files[0]);
    }
  });

  /* Keyboard activation */
  dropzone.addEventListener('keydown', e => {
    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); }
  });
}

bindFileInput('profilePhoto', 'dropzone-profile', 'preview-profile');
bindFileInput('idProof',      'dropzone-id',      'preview-id');
bindFileInput('sslcCert',     'dropzone-sslc',    'preview-sslc');
bindFileInput('hscCert',      'dropzone-hsc',     'preview-hsc');

/* ---- SECTION VALIDATION ---- */

function validateSection(sectionId) {
  const section = document.getElementById('section-' + sectionId);
  const inputs  = section.querySelectorAll('[required]');
  let valid = true;
  inputs.forEach(input => { if (!validateField(input)) valid = false; });
  return valid;
}

/* ---- NAVIGATION BINDINGS ---- */

function bindNext(btnId, sectionId, nextIdx) {
  const btn = document.getElementById(btnId);
  if (!btn) return;
  btn.addEventListener('click', () => {
    if (validateSection(sectionId)) nextSection();
  });
}

function bindPrev(btnId, prevIdx) {
  const btn = document.getElementById(btnId);
  if (!btn) return;
  btn.addEventListener('click', () => prevSection());
}

bindNext('nextPersonal',  'personal',    1);
bindPrev('prevContact',   0);
bindNext('nextContact',   'contact',     2);
bindPrev('prevAcademic',  1);
bindNext('nextAcademic',  'academic',    3);
bindPrev('prevCourse',    2);
bindNext('nextCourse',    'course',      4);
bindPrev('prevAccount',   3);
bindNext('nextAccount',   'account',     5);
bindPrev('prevDocuments', 4);
bindNext('nextDocuments', 'documents',   6);
bindPrev('prevDeclaration', 5);

/* Sidebar progress step buttons */
document.querySelectorAll('.progress-step').forEach(btn => {
  btn.addEventListener('click', () => {
    const idx = sections.indexOf(btn.dataset.section);
    if (idx <= currentIdx) showSection(idx);
  });
});

/* ---- FORM SUBMISSION ---- */

document.getElementById('admissionForm').addEventListener('submit', function(e) {
  e.preventDefault();

  /* Validate all sections in order */
  let allValid = true;
  sections.forEach(id => { if (!validateSection(id)) allValid = false; });

  if (!allValid) {
    /* Navigate to first section with errors */
    for (let i = 0; i < sections.length; i++) {
      const section = document.getElementById('section-' + sections[i]);
      if (section.querySelector('.error') || section.querySelector('.error-msg.visible')) {
        showSection(i);
        break;
      }
    }
    return;
  }

  const submitBtn = document.getElementById('submitBtn');
  const originalHTML = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = 'Submitting Application...';

  const formData = new FormData(this);

  fetch('submit.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHTML;

    if (data.success) {
      document.querySelectorAll('.progress-step').forEach(b => b.classList.add('done'));
      document.getElementById('admissionForm').hidden = true;
      const successEl = document.getElementById('successState');
      successEl.hidden = false;
      const appRef = document.getElementById('successAppRef');
      if (appRef) appRef.textContent = data.roll_number || 'Registered';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      alert('Application Submission Notice:\n' + (data.error || 'Unable to submit application.'));
    }
  })
  .catch(err => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHTML;
    console.error('Submission error:', err);
    alert('An unexpected network error occurred while submitting your application. Please check your connection and try again.');
  });
});

/* ---- RESET FORM ---- */

document.getElementById('resetForm').addEventListener('click', function() {
  if (!confirm('Are you sure you want to reset the entire form? All entered data will be lost.')) return;
  resetForm();
});

document.getElementById('applyAgain')?.addEventListener('click', resetForm);

function resetForm() {
  const form = document.getElementById('admissionForm');
  form.reset();

  /* Clear validation states */
  form.querySelectorAll('.form-control').forEach(el => el.classList.remove('error','success'));
  form.querySelectorAll('.error-msg').forEach(el => el.classList.remove('visible'));

  /* Clear file previews */
  ['preview-profile','preview-id','preview-sslc','preview-hsc'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.hidden = true;
  });
  ['dropzone-profile','dropzone-id','dropzone-sslc','dropzone-hsc'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.classList.remove('has-file','error-state');
  });

  /* Reset password strength */
  document.getElementById('strengthFill').className = 'strength-fill';
  document.getElementById('strengthFill').style.width = '0';
  document.getElementById('strengthLabel').textContent = 'Enter a password';
  document.querySelectorAll('.password-hints li').forEach(li => li.classList.remove('met'));

  /* Reset nationality selector */
  const natSel = document.getElementById('nationalitySelect');
  const otherGrp = document.getElementById('otherNationalityGroup');
  const otherInp = document.getElementById('otherNationality');
  const hidNat = document.getElementById('nationality');
  if (natSel) natSel.value = 'Indian';
  if (otherGrp) otherGrp.style.display = 'none';
  if (otherInp) { otherInp.value = ''; otherInp.removeAttribute('required'); }
  if (hidNat) hidNat.value = 'Indian';

  /* Reset progress */
  document.querySelectorAll('.progress-step').forEach(b => b.classList.remove('done'));

  /* Show form, hide success */
  form.hidden = false;
  document.getElementById('successState').hidden = true;

  showSection(0);
}

/* ---- MOBILE NAV TOGGLE ---- */

const navToggle = document.getElementById('navToggle');
const navLinks  = document.getElementById('navLinks');
navToggle.addEventListener('click', () => {
  const open = navLinks.classList.toggle('open');
  navToggle.setAttribute('aria-expanded', open);
});

/* ---- HELPER: XSS ESCAPE ---- */

function escapeHTML(str) {
  return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

/* ---- DYNAMIC NATIONALITY SELECTOR ---- */
const natSelect = document.getElementById('nationalitySelect');
const otherGroup = document.getElementById('otherNationalityGroup');
const otherInput = document.getElementById('otherNationality');
const hiddenNat  = document.getElementById('nationality');

if (natSelect && otherGroup && otherInput && hiddenNat) {
  natSelect.addEventListener('change', () => {
    if (natSelect.value === 'Other') {
      otherGroup.style.display = 'block';
      otherInput.setAttribute('required', 'required');
      otherInput.focus();
      hiddenNat.value = otherInput.value.trim();
    } else {
      otherGroup.style.display = 'none';
      otherInput.removeAttribute('required');
      otherInput.value = '';
      hiddenNat.value = 'Indian';
      otherInput.classList.remove('error');
      const err = document.getElementById('err-otherNationality');
      if (err) err.classList.remove('visible');
    }
  });

  otherInput.addEventListener('input', () => {
    hiddenNat.value = otherInput.value.trim();
    if (otherInput.value.trim().length >= 2) {
      otherInput.classList.remove('error');
      const err = document.getElementById('err-otherNationality');
      if (err) err.classList.remove('visible');
    }
  });
}

