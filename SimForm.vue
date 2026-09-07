<template>
  <div class="sim-form-container">
    <header class="header">
      <div class="logo-area">
        <span class="company-name">Hakuhodo DY ONE</span>
        <span class="app-title">SIM FORM</span>
      </div>
      <nav class="stepper">
        <div class="step" :class="{ active: currentStep === 1 }">ENTRY</div>
        <div class="step" :class="{ active: currentStep === 2 }">PLATFORMS</div>
        <div class="step" :class="{ active: currentStep === 3 }">FORM</div>
      </nav>
    </header>

    <main class="content-wrapper">
      <transition name="fade-slide" mode="out-in">
        <!-- STEP 1 -->
        <section v-if="currentStep === 1" key="step1" class="step-panel step1-content">
          <div class="title-area">
            <h2>ENTRY</h2>
            <span class="required-label">*必須入力</span>
          </div>
          <div class="form-body-vertical">
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(1, formData.applicantName) }">
              <label class="unified-font"><span class="num">1</span> <span class="req">*</span>依頼者名</label>
              <input ref="applicantNameInput" v-model="formData.applicantName" type="text" class="unified-font transparent-input" @focus="activeFieldIndex = 1" placeholder="例：山田 太郎" />
            </div>
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(2, formData.department) }">
              <label class="unified-font"><span class="num">2</span> 所属</label>
              <div class="radio-row">
                <label v-for="option in departmentOptions" :key="option.value" class="radio-item" :class="{ 'is-selected': formData.department === option.value }">
                  <input v-model="formData.department" type="radio" name="department" :value="option.value" @focus="activeFieldIndex = 2" />
                  <span class="custom-radio"></span><span class="radio-label-text unified-font">{{ option.label }}</span>
                </label>
              </div>
            </div>
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(3, formData.email) }">
              <label class="unified-font"><span class="num">3</span> Email</label>
              <input v-model="formData.email" type="email" class="unified-font transparent-input" :class="{ 'is-error': isEmailInvalid }" placeholder="example@domain.com" autocomplete="off" @focus="activeFieldIndex = 3" @blur="touched.email = true" />
              <p v-if="isEmailInvalid" class="error-text">有効なメールアドレスを入力してください</p>
            </div>
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(4, formData.clientName) }">
              <label class="unified-font"><span class="num">4</span> <span class="req">*</span>クライアント名</label>
              <input v-model="formData.clientName" type="text" class="unified-font transparent-input" placeholder="例：株式会社Hakuhodo DY ONE" @focus="activeFieldIndex = 4" />
            </div>
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(5, formData.projectName) }">
              <label class="unified-font"><span class="num">5</span> <span class="req">*</span>案件名</label>
              <input v-model="formData.projectName" type="text" class="unified-font transparent-input" placeholder="入力してください" @focus="activeFieldIndex = 5" />
            </div>
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(6, formData.dueDate) }">
              <label class="unified-font"><span class="num">6</span> 希望納期</label>
              <input v-model="formData.dueDate" type="date" class="unified-font custom-date-input transparent-input" @focus="activeFieldIndex = 6" />
            </div>
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(7, formData.remarks) }">
              <label class="unified-font"><span class="num">7</span> 備考</label>
              <textarea v-model="formData.remarks" class="unified-font custom-textarea transparent-input" placeholder="補足事項があれば入力してください" @focus="activeFieldIndex = 7"></textarea>
            </div>
          </div>
          <div class="action-area">
            <button class="next-btn animated-btn" :class="{ visible: isStep1Valid }" :disabled="!isStep1Valid" @click="currentStep = 2"><span>PLATFORMS 選択へ進む</span><span class="btn-arrow">&rarr;</span></button>
          </div>
        </section>

        <!-- STEP 2 -->
        <section v-else-if="currentStep === 2" key="step2" class="step-panel step2-content">
          <div class="platform-header">
            <h2>PLATFORMS</h2>
            <span class="sub-label">依頼するプラットフォームを選択し、マージンを設定してください</span>
          </div>
          <div class="platform-grid-container">
            <div class="platform-label-column">
              <div class="label-header">媒体</div><div class="label-cell">種別</div><div class="label-cell stripe">マージン</div>
            </div>
            <div class="platform-columns-wrapper">
              <div v-for="platform in platformList" :key="platform.id" class="platform-column">
                <div class="chip-cell">
                  <div class="platform-chip" :class="{ selected: platforms[platform.id].selected }" @click="togglePlatform(platform.id)" @mouseenter="onMouseEnter(platform.id)" @mouseleave="onMouseLeave(platform.id)">
                    <div class="chip-logos">
                      <div v-for="(logo, idx) in platform.logos" :key="idx" class="lottie-wrapper">
                        <DotLottieVue :src="logo.lottiePath" :loop="true" :ref="el => setLottieRef(el, platform.id)" class="lottie-player" />
                      </div>
                    </div>
                    <span class="platform-chip-name">{{ platform.name }}</span>
                  </div>
                </div>
                <div class="grid-cell"><select v-model="platforms[platform.id].marginType" class="margin-select-line" :disabled="!platforms[platform.id].selected"><option value="percent">% (グロス)</option><option value="fixed">定額</option></select></div>
                <div class="grid-cell stripe"><div class="margin-input-wrap-inline"><input v-model.number="platforms[platform.id].marginValue" type="number" class="margin-input-line transparent-input" placeholder="20" :disabled="!platforms[platform.id].selected" /><span class="margin-suffix-text">{{ platforms[platform.id].marginType === 'fixed' ? '円' : '%' }}</span></div></div>
              </div>
            </div>
          </div>
          <div class="action-area step2-actions">
            <button class="back-btn" @click="currentStep = 1">&larr; ENTRYに戻る</button>
            <button class="next-btn animated-btn visible" :disabled="!isStep2Valid" @click="currentStep = 3"><span>FORM 入力へ進む</span><span class="btn-arrow">&rarr;</span></button>
          </div>
        </section>

        <!-- STEP 3 -->
        <section v-else-if="currentStep === 3" key="step3" class="step-panel step3-content">
          <div class="platform-header">
            <h2>FORM DETAILS</h2>
            <span class="sub-label">YouTubeの詳細パラメータを設定してください</span>
          </div>

          <div v-if="platforms.youtube.selected" class="youtube-form-container">
            <div class="pattern-list-card">
              <div class="pattern-list-header">
                <div><strong>YOUTUBE PATTERNS</strong><span>{{ youtubePatterns.length }} pattern{{ youtubePatterns.length > 1 ? 's' : '' }}</span></div>
                <p>各パターンをクリックして詳細を入力・編集できます</p>
              </div>

              <div class="pattern-table-wrap">
                <table class="pattern-list-table">
                  <colgroup><col class="op-col"><col class="num-col"><col class="menu-col"><col class="placement-col"><col class="creative-col"><col class="period-col"><col class="budget-col"><col class="area-col"><col class="gender-col"><col class="device-col"></colgroup>
                  <thead><tr><th>操作</th><th>#</th><th>メニュー</th><th>配信面</th><th>クリエイティブ</th><th>配信期間</th><th>予算</th><th>エリア</th><th>性別</th><th>デバイス</th></tr></thead>
                  <tbody>
                    <tr v-for="(pattern, index) in youtubePatterns" :key="pattern.id">
                      <td class="pattern-actions">
                        <button type="button" class="icon-action edit-action" aria-label="パターンを編集" title="編集" @click="openYoutubePattern(index)"><svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg></button>
                        <button type="button" class="icon-action delete-action" aria-label="削除" title="削除" :disabled="youtubePatterns.length <= 1" @click="removeYoutubePattern(index)"><svg viewBox="0 0 24 24"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></button>
                        <button type="button" class="icon-action copy-action" aria-label="複製" title="複製" @click="copyYoutubePattern(index)"><svg viewBox="0 0 24 24"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M6 15H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1"/></svg></button>
                      </td>
                      <td class="num-cell">{{ index + 1 }}</td>
                      <td>{{ pattern.menu || '未設定' }}</td>
                      <td>{{ pattern.placement || '未設定' }}</td>
                      <td>{{ creativeSummary(pattern) }}</td>
                      <td>{{ pattern.periodNumber ? `${pattern.periodNumber}${pattern.periodUnit}` : '未設定' }}</td>
                      <td class="budget-cell">{{ formatBudget(pattern.budget) }}</td>
                      <td>{{ pattern.prefNames.length ? pattern.prefNames.join(' / ') : '未設定' }}<span v-if="pattern.city">・{{ pattern.city }}</span></td>
                      <td>{{ genderLabel(pattern.gender) }}</td>
                      <td>{{ deviceLabel(pattern.device) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="add-pattern-area"><button type="button" class="add-new-btn" @click="addYoutubePattern"><span>＋</span> New Pattern</button></div>
            </div>
          </div>
          <div v-else class="empty-platform-message">PLATFORMSでYouTubeを選択してください。</div>

          <div class="action-area step2-actions">
            <button class="back-btn" @click="currentStep = 2">&larr; PLATFORMSに戻る</button>
            <button class="next-btn animated-btn visible" @click="submitForm"><span>送信する</span><span class="btn-arrow">&rarr;</span></button>
          </div>
        </section>
      </transition>
    </main>

    <!-- YouTube パターン入力モーダル -->
    <div v-if="patternModalOpen" class="modal-overlay" @click.self="closeYoutubePattern">
      <div class="modal-content pattern-modal">
        <div class="modal-header">
          <div><span class="modal-kicker">YOUTUBE / PATTERN {{ editingPatternIndex + 1 }}</span><h3>{{ isNewPattern ? '新しいパターンを設定' : 'パターンを編集' }}</h3><p>上から順番に入力してください</p></div>
          <button type="button" class="btn-close" aria-label="閉じる" @click="closeYoutubePattern">×</button>
        </div>

        <div class="modal-form-body">
          <div class="modal-form-group">
            <label><span class="modal-num">1</span><span class="req">*</span>メニュー</label>
            <select v-model="editingPattern.menu" class="modal-input"><option value="">選択してください</option><option v-for="value in youtubeMenus" :key="value" :value="value">{{ value }}</option></select>
          </div>

          <div class="modal-form-group">
            <label><span class="modal-num">2</span><span class="req">*</span>配信面</label>
            <select v-model="editingPattern.placement" class="modal-input"><option value="">選択してください</option><option v-for="value in currentPlacementOptions" :key="value" :value="value">{{ value }}</option></select>
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group"><label><span class="modal-num">3</span>縦型</label><select v-model="editingPattern.verticalCreative" class="modal-input"><option value="">選択してください</option><option v-for="value in creativeOptionsForPattern" :key="`v-${value}`" :value="value">{{ value }}</option></select></div>
            <div class="modal-form-group"><label><span class="modal-num">4</span>横型</label><select v-model="editingPattern.horizontalCreative" class="modal-input"><option value="">選択してください</option><option v-for="value in creativeOptionsForPattern" :key="`h-${value}`" :value="value">{{ value }}</option></select></div>
          </div>

          <div class="modal-form-group">
            <label><span class="modal-num">5</span>配信期間</label>
            <div class="modal-inline"><input v-model.number="editingPattern.periodNumber" type="number" min="1" class="modal-input period-modal-number" placeholder="数値" /><select v-model="editingPattern.periodUnit" class="modal-input"><option value="日">日</option><option value="週">週</option><option value="月">月</option></select></div>
          </div>

          <div class="modal-form-group">
            <label><span class="modal-num">6</span><span class="req">*</span>予算</label>
            <div class="budget-input-wrap"><input v-model.number="editingPattern.budget" type="number" min="0" class="modal-input" placeholder="1000000" /><span>円</span></div>
          </div>

          <div class="modal-form-group">
            <label><span class="modal-num">7</span>エリア</label>
            <button type="button" class="modal-select-button" @click="openPrefModalForEditing"><span v-if="editingPattern.prefNames.length">{{ editingPattern.prefNames.join(' / ') }}</span><span v-else class="placeholder">都道府県を選択</span><span>›</span></button>
            <input v-model="editingPattern.city" type="text" class="modal-input city-input" :disabled="editingPattern.prefNames.includes('全国')" placeholder="市区町村（任意）" />
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group"><label><span class="modal-num">8</span>性別</label><select v-model="editingPattern.gender" class="modal-input"><option value="all">すべて</option><option value="male">男性</option><option value="female">女性</option></select></div>
            <div class="modal-form-group"><label><span class="modal-num">9</span>デバイス</label><select v-model="editingPattern.device" class="modal-input"><option value="all">すべて</option><option value="mobile">モバイル</option><option value="desktop">PC</option><option value="tv">コネクテッドTV</option></select></div>
          </div>

          <div class="modal-form-group">
            <label><span class="modal-num">10</span>年齢</label>
            <div class="age-modal-row">
              <div><input v-model="editingPattern.age1" type="number" min="0" max="100" class="modal-input" placeholder="年齢①" /><span>歳</span></div>
              <div><input v-model="editingPattern.age2" type="number" min="0" max="100" class="modal-input" placeholder="年齢②" /><span>歳</span></div>
              <div><input v-model="editingPattern.age3" type="number" min="0" max="100" class="modal-input" placeholder="年齢③" /><span>歳</span></div>
            </div>
          </div>

          <div class="modal-form-group">
            <label><span class="modal-num">11</span>ターゲット</label>
            <div class="target-section">
              <div v-for="column in [1, 2, 3]" :key="column" class="target-block">
                <span class="target-label">ターゲット{{ ['①','②','③'][column - 1] }}</span>
                <div class="target-options">
                  <button v-for="value in targetLists[column]" :key="value" type="button" class="target-chip" :class="{ selected: editingPattern[`target${column}`].includes(value) }" @click="toggleEditingTarget(column, value)">{{ value }}</button>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-form-group">
            <label><span class="modal-num">12</span>備考</label>
            <textarea v-model="editingPattern.notes" class="modal-textarea" placeholder="補足事項があれば入力してください"></textarea>
          </div>
        </div>

        <div class="modal-footer pattern-modal-footer">
          <button type="button" class="modal-cancel-btn" @click="closeYoutubePattern">キャンセル</button>
          <button type="button" class="modal-save-btn" @click="saveYoutubePattern"><span>保存する</span><span>→</span></button>
        </div>
      </div>
    </div>

    <!-- 都道府県選択 -->
    <div v-if="prefModalOpen" class="modal-overlay nested-overlay" @click.self="closePrefModal">
      <div class="modal-content pref-modal">
        <div class="modal-header"><div><span class="modal-kicker">AREA</span><h3>都道府県を選択</h3></div><button type="button" class="btn-close" @click="closePrefModal">×</button></div>
        <div class="modal-toolbar"><input v-model="prefSearch" type="search" placeholder="都道府県を検索" /><button type="button" @click="pendingPrefNames = []">クリア</button></div>
        <div class="modal-selected-header">選択済み</div>
        <div class="modal-selected-body"><span v-for="name in pendingPrefNames" :key="name" class="selected-chip">{{ name }} <button type="button" @click="removePendingPref(name)">×</button></span><span v-if="!pendingPrefNames.length" class="empty-selected">未選択</span></div>
        <div class="pref-grid"><button v-for="pref in filteredPrefList" :key="pref.id" type="button" class="pref-option" :class="{ selected: pendingPrefNames.includes(pref.name) }" @click="togglePendingPref(pref)">{{ pref.name }}</button></div>
        <div class="modal-footer"><button type="button" class="modal-cancel-btn" @click="closePrefModal">キャンセル</button><button type="button" class="modal-save-btn small" @click="confirmPrefModal">確定</button></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, ref, onMounted, watch } from 'vue'
import { DotLottieVue } from '@lottiefiles/dotlottie-vue'

const currentStep = ref(1)
const activeFieldIndex = ref(1)
const applicantNameInput = ref(null)
const hoveredPlatform = ref(null)
const lottieRefs = reactive({})

onMounted(() => applicantNameInput.value?.focus())

const formData = reactive({ applicantName: '', department: '', email: '', clientName: '', projectName: '', dueDate: '', remarks: '' })
const touched = reactive({ email: false })
const departmentOptions = [
  { value: 'dept1', label: '第1営業部' },
  { value: 'dept2', label: '第2営業部' },
  { value: 'dev', label: '開発部' }
]

const isEmailInvalid = computed(() => {
  if (!formData.email.trim()) return false
  return touched.email && !/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(formData.email.trim())
})
const isStep1Valid = computed(() => formData.applicantName.trim() && formData.clientName.trim() && formData.projectName.trim() && !isEmailInvalid.value)
const isFieldDimmed = (index, value) => activeFieldIndex.value !== index && (!value || (typeof value === 'string' && !value.trim()))

const platformList = [
  { id: 'meta', name: 'META', logos: [{ lottiePath: '/images_json/Meta.lottie' }] },
  { id: 'listing', name: 'LISTING', logos: [{ lottiePath: '/images_json/Listing.lottie' }] },
  { id: 'youtube', name: 'YOUTUBE', logos: [{ lottiePath: '/images_json/YouTube.lottie' }] }
]
const platforms = reactive(Object.fromEntries(platformList.map(p => [p.id, { selected: false, marginType: 'percent', marginValue: 20 }])))
const isStep2Valid = computed(() => Object.values(platforms).some(p => p.selected))
const setLottieRef = (el, id) => { if (el) lottieRefs[id] = el }
const getDotLottie = id => lottieRefs[id]?.getDotLottieInstance?.() || null
const onMouseEnter = id => { hoveredPlatform.value = id; getDotLottie(id)?.play() }
const onMouseLeave = id => { hoveredPlatform.value = null; if (!platforms[id].selected) getDotLottie(id)?.stop() }
const togglePlatform = id => { platforms[id].selected = !platforms[id].selected; const l = getDotLottie(id); if (l) platforms[id].selected ? l.play() : l.stop() }

let youtubePatternSeq = 1
const createEmptyPattern = () => ({
  id: `yt-${Date.now()}-${youtubePatternSeq++}`,
  menu: '', placement: '', verticalCreative: '', horizontalCreative: '', periodNumber: null, periodUnit: '日', budget: null,
  prefId: '', prefName: '', prefNames: [], city: '', gender: 'all', age1: '', age2: '', age3: '', target1: [], target2: [], target3: [], device: 'all', notes: ''
})
const youtubePatterns = reactive([createEmptyPattern()])

const youtubeMenus = ['VRC2.0（リーチ）', 'VVC（視聴）', 'スキップ不可', '目標FQ（マルチフォーマット）', '目標FQ（スキップ可）', '目標FQ（スキップ不可）']
const placementOptionsByMenu = {
  'VRC2.0（リーチ）': ['インストリーム', 'バンパー', 'インフィード', 'ショート'],
  'VVC（視聴）': ['インストリーム', 'インフィード', 'ショート'],
  'スキップ不可': ['インストリーム'],
  '目標FQ（マルチフォーマット）': ['インストリーム', 'バンパー', 'インフィード', 'ショート'],
  '目標FQ（スキップ可）': ['インストリーム', 'バンパー'],
  '目標FQ（スキップ不可）': ['インストリーム']
}
const creativeOptions = ['なし', '6s＊バンパー', '15s', '30s', '45s', '60s', '90s', '120s']
const currentPlacementOptions = computed(() => placementOptionsByMenu[editingPattern.menu] || [])
const creativeOptionsForPattern = computed(() => ['スキップ不可', '目標FQ（スキップ不可）'].includes(editingPattern.menu) ? ['なし', '15s', '30s ※CTVのみ', '6sバンパー+15s ※β版'] : creativeOptions)

const prefList = [
  ['national','全国'],['hokkaido','北海道'],['aomori','青森県'],['iwate','岩手県'],['miyagi','宮城県'],['akita','秋田県'],['yamagata','山形県'],['fukushima','福島県'],['ibaraki','茨城県'],['tochigi','栃木県'],['gunma','群馬県'],['saitama','埼玉県'],['chiba','千葉県'],['tokyo','東京都'],['kanagawa','神奈川県'],['niigata','新潟県'],['toyama','富山県'],['ishikawa','石川県'],['fukui','福井県'],['yamanashi','山梨県'],['nagano','長野県'],['gifu','岐阜県'],['shizuoka','静岡県'],['aichi','愛知県'],['mie','三重県'],['shiga','滋賀県'],['kyoto','京都府'],['osaka','大阪府'],['hyogo','兵庫県'],['nara','奈良県'],['wakayama','和歌山県'],['tottori','鳥取県'],['shimane','島根県'],['okayama','岡山県'],['hiroshima','広島県'],['yamaguchi','山口県'],['tokushima','徳島県'],['kagawa','香川県'],['ehime','愛媛県'],['kochi','高知県'],['fukuoka','福岡県'],['saga','佐賀県'],['nagasaki','長崎県'],['kumamoto','熊本県'],['oita','大分県'],['miyazaki','宮崎県'],['kagoshima','鹿児島県'],['okinawa','沖縄県']
].map(([id, name]) => ({ id, name }))

const targetLists = {
  1: ['アフィニティカテゴリ','スポーツファン','旅行好き','テクノロジー愛好者','美容・ファッション','グルメ・料理','自動車愛好者','ゲーム愛好者','音楽ファン','ニュース好き','アウトドア好き'],
  2: ['購買意向の強いオーディエンス','家電・電子機器','自動車','旅行','金融サービス','不動産','求人・転職','教育','美容商品','通信サービス','ソフトウェア'],
  3: ['詳細なユーザー属性','子どもあり','大学生','会社員','経営者','住宅所有者','最近結婚した','最近引っ越した','親','世帯収入上位層']
}

const patternModalOpen = ref(false)
const editingPatternIndex = ref(0)
const editingPattern = reactive(createEmptyPattern())
const isNewPattern = ref(false)
const clonePattern = pattern => JSON.parse(JSON.stringify(pattern))
const openYoutubePattern = index => {
  editingPatternIndex.value = index
  Object.assign(editingPattern, clonePattern(youtubePatterns[index]))
  isNewPattern.value = false
  patternModalOpen.value = true
}
const addYoutubePattern = () => {
  editingPatternIndex.value = youtubePatterns.length
  Object.assign(editingPattern, createEmptyPattern())
  isNewPattern.value = true
  patternModalOpen.value = true
}
const closeYoutubePattern = () => { patternModalOpen.value = false }
const saveYoutubePattern = () => {
  if (isNewPattern.value) youtubePatterns.push(clonePattern(editingPattern))
  else youtubePatterns[editingPatternIndex.value] = clonePattern(editingPattern)
  closeYoutubePattern()
}
const removeYoutubePattern = index => { if (youtubePatterns.length > 1) youtubePatterns.splice(index, 1) }
const copyYoutubePattern = index => { const copy = clonePattern(youtubePatterns[index]); copy.id = `yt-${Date.now()}-${youtubePatternSeq++}`; youtubePatterns.splice(index + 1, 0, copy) }
const creativeSummary = p => [p.verticalCreative && `縦:${p.verticalCreative}`, p.horizontalCreative && `横:${p.horizontalCreative}`].filter(Boolean).join(' / ') || '未設定'
const formatBudget = value => value === null || value === '' ? '未設定' : `￥${Number(value).toLocaleString('ja-JP')}`
const genderLabel = value => ({ all: 'すべて', male: '男性', female: '女性' }[value] || '未設定')
const deviceLabel = value => ({ all: 'すべて', mobile: 'モバイル', desktop: 'PC', tv: 'CTV' }[value] || '未設定')
const toggleEditingTarget = (column, value) => {
  const key = `target${column}`
  editingPattern[key] = editingPattern[key].includes(value) ? editingPattern[key].filter(v => v !== value) : [...editingPattern[key], value]
}
watch(() => editingPattern.menu, () => { if (!currentPlacementOptions.value.includes(editingPattern.placement)) editingPattern.placement = '' })

const prefModalOpen = ref(false)
const pendingPrefNames = ref([])
const prefSearch = ref('')
const filteredPrefList = computed(() => { const q = prefSearch.value.trim().toLowerCase(); return q ? prefList.filter(p => p.name.toLowerCase().includes(q)) : prefList })
const openPrefModalForEditing = () => { pendingPrefNames.value = [...editingPattern.prefNames]; prefSearch.value = ''; prefModalOpen.value = true }
const closePrefModal = () => { prefModalOpen.value = false }
const togglePendingPref = pref => {
  if (pref.name === '全国') pendingPrefNames.value = pendingPrefNames.value.includes('全国') ? [] : ['全国']
  else {
    pendingPrefNames.value = pendingPrefNames.value.filter(v => v !== '全国')
    pendingPrefNames.value = pendingPrefNames.value.includes(pref.name) ? pendingPrefNames.value.filter(v => v !== pref.name) : [...pendingPrefNames.value, pref.name]
  }
}
const removePendingPref = name => { pendingPrefNames.value = pendingPrefNames.value.filter(v => v !== name) }
const confirmPrefModal = () => { editingPattern.prefNames = [...pendingPrefNames.value]; editingPattern.prefId = editingPattern.prefNames[0] || ''; editingPattern.prefName = editingPattern.prefNames.join(' / '); if (editingPattern.prefNames.includes('全国')) editingPattern.city = ''; closePrefModal() }

const submitForm = () => {
  console.log('送信データ:', { entry: formData, platforms, youtube: { patterns: youtubePatterns } })
  alert('送信が完了しました！')
}
</script>

<style scoped>
.sim-form-container{font-family:"Barlow","Noto Sans JP",-apple-system,BlinkMacSystemFont,sans-serif;color:#111;background:#fff;min-height:100vh;display:flex;flex-direction:column;box-sizing:border-box}.header{position:sticky;top:0;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f0f0f0;padding:0 1.5rem 0 2.5rem;z-index:100;background:rgba(255,255,255,.95);backdrop-filter:blur(8px);height:64px}.logo-area{display:flex;align-items:center;gap:12px;font-weight:700}.company-name{font-size:1rem;letter-spacing:-.02em}.app-title{font-size:1rem;color:#888;letter-spacing:.05em;font-weight:400}.stepper{display:flex;height:100%}.step{padding:0 24px;display:flex;align-items:center;font-size:.75rem;font-weight:700;letter-spacing:.1em;color:#aaa}.step.active{background:#111;color:#fff}.content-wrapper{flex:1;width:100%;display:flex;justify-content:center;padding:48px 20px 96px;box-sizing:border-box}.step-panel{width:100%;box-sizing:border-box}.step1-content{max-width:680px}.step2-content,.step3-content{max-width:1400px}.title-area h2,.platform-header h2{font-size:2.2rem;font-weight:800;margin:0;letter-spacing:-.03em}.required-label,.sub-label{font-size:.75rem;color:#888;display:block;margin-top:6px}.form-body-vertical{margin-top:40px;display:flex;flex-direction:column;gap:40px}.form-group{opacity:1;transition:opacity .35s}.form-group.is-dimmed{opacity:.25}.form-group label{display:flex;align-items:center;font-weight:600;margin-bottom:10px;color:#111;white-space:nowrap}.num{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;background:#111;color:#fff;border-radius:50%;font-size:.72rem;font-weight:700;margin-right:10px;flex-shrink:0}.req{color:#e03131;margin-right:4px}.unified-font{font-size:.95rem!important;line-height:1.5}.form-group input[type=text],.form-group input[type=email],.custom-date-input{width:100%;padding:12px 0;border:none;border-bottom:1px solid #e0e0e0;color:#111;outline:none;box-sizing:border-box}.custom-textarea{width:100%;height:120px;padding:12px 0;border:none;border-bottom:1px solid #e0e0e0;border-radius:0;color:#111;outline:none;resize:vertical;font-family:inherit;box-sizing:border-box}.custom-textarea:focus,.form-group input:focus,.custom-date-input:focus{border-bottom:2px solid #111}.error-text{color:#e03131;font-size:.75rem;margin-top:6px}.radio-row{display:flex;gap:12px;margin-top:6px;flex-wrap:wrap}.radio-item{flex:1;min-width:100px;display:flex;align-items:center;padding:10px 8px;border-radius:6px;cursor:pointer}.radio-item input{position:absolute;opacity:0}.custom-radio{width:14px;height:14px;border:2px solid #ccc;border-radius:50%;margin-right:6px;display:flex;align-items:center;justify-content:center}.custom-radio:after{content:'';width:6px;height:6px;background:#111;border-radius:50%;opacity:0;transform:scale(.5)}.radio-item.is-selected .custom-radio{border-color:#111}.radio-item.is-selected .custom-radio:after{opacity:1;transform:scale(1)}.radio-label-text{font-size:.9rem}.action-area{margin-top:48px;display:flex;justify-content:flex-end}.next-btn,.back-btn{border:none;cursor:pointer;font-family:inherit}.next-btn{display:inline-flex;align-items:center;gap:16px;background:#111;color:#fff;padding:14px 22px;border-radius:8px;font-size:.85rem;font-weight:700}.next-btn:disabled{opacity:.25;cursor:not-allowed}.animated-btn{border:1px solid #111;padding:16px 56px;font-size:.95rem;letter-spacing:.06em;border-radius:6px}.btn-arrow{font-size:1.1rem}.back-btn{background:transparent;color:#111;border:1px solid #111;padding:16px 32px;font-size:.85rem;border-radius:6px}.step2-actions{justify-content:space-between;align-items:center}.platform-header{margin-bottom:40px}.platform-grid-container{display:flex;width:100%;border-top:1px solid #e8e8e8;border-left:1px solid #e8e8e8}.platform-label-column{width:100px;flex-shrink:0}.label-header,.label-cell{display:flex;align-items:center;justify-content:center;border-right:1px solid #e8e8e8;border-bottom:1px solid #e8e8e8;font-size:.75rem;font-weight:700}.label-header{height:110px;background:#111;color:#fff}.label-cell{height:82px;color:#666;background:#fafafa}.label-cell.stripe{background:#f5f5f5}.platform-columns-wrapper{display:flex;flex:1}.platform-column{flex:1;min-width:0}.chip-cell,.grid-cell{border-right:1px solid #e8e8e8;border-bottom:1px solid #e8e8e8}.chip-cell{height:110px;display:flex;align-items:center;justify-content:center;padding:12px;box-sizing:border-box}.platform-chip{width:100%;height:72px;border:1px solid #e1e1e1;border-radius:12px;display:flex;align-items:center;justify-content:center;gap:8px;cursor:pointer;background:#fff;transition:.25s}.platform-chip:hover{border-color:#111;transform:translateY(-2px)}.platform-chip.selected{background:#111;color:#fff;border-color:#111}.chip-logos{display:flex;align-items:center}.lottie-wrapper{width:30px;height:30px;overflow:hidden}.lottie-player{width:100%;height:100%}.platform-chip-name{font-size:.75rem;font-weight:800;letter-spacing:.08em}.grid-cell{height:82px;display:flex;align-items:center;justify-content:center;padding:10px;box-sizing:border-box}.grid-cell.stripe{background:#fafafa}.margin-select-line{width:100%;border:none;background:transparent;outline:none;font-family:inherit;text-align:center}.margin-input-wrap-inline{display:flex;align-items:center;justify-content:center;width:100%}.margin-input-line{width:70px;border:none;border-bottom:1px solid #d8d8d8;padding:8px 4px;outline:none;text-align:right;background:transparent}.margin-suffix-text{font-size:.8rem;margin-left:4px;color:#666}.youtube-form-container{width:100%}.pattern-list-card{border:1px solid #e5e5e5;border-radius:16px;background:#fff;overflow:hidden}.pattern-list-header{display:flex;justify-content:space-between;align-items:flex-end;padding:22px 24px;border-bottom:1px solid #eee}.pattern-list-header strong{font-size:14px;letter-spacing:.08em}.pattern-list-header span{margin-left:10px;color:#999;font-size:12px}.pattern-list-header p{margin:0;color:#888;font-size:12px}.pattern-table-wrap{width:100%;overflow-x:auto}.pattern-list-table{width:100%;min-width:1100px;border-collapse:collapse;table-layout:fixed}.pattern-list-table th{height:48px;background:linear-gradient(180deg,#222,#111);color:#fff;border-right:1px solid #3d3d3d;font-size:11px;text-align:center}.pattern-list-table td{height:78px;padding:10px 12px;border-right:1px solid #e5e5e5;border-bottom:1px solid #e5e5e5;font-size:12px;vertical-align:middle;overflow:hidden;text-overflow:ellipsis}.pattern-list-table tbody tr:nth-child(even) td{background:#fafafa}.op-col{width:126px}.num-col{width:42px}.menu-col{width:190px}.placement-col{width:130px}.creative-col{width:180px}.period-col{width:100px}.budget-col{width:125px}.area-col{width:190px}.gender-col{width:90px}.device-col{width:100px}.num-cell{text-align:center;font-weight:700}.budget-cell{text-align:right;font-weight:600}.pattern-actions{padding:0!important;display:flex;align-items:stretch;justify-content:center}.icon-action{width:42px;height:78px;border:0;background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer}.icon-action svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.edit-action{color:#111}.delete-action{color:#c94b4b}.copy-action{color:#555}.icon-action:disabled{opacity:.2;cursor:not-allowed}.icon-action:hover:not(:disabled){background:#f5f5f5}.add-pattern-area{display:flex;justify-content:center;padding:20px}.add-new-btn{border:1px solid #d7d7d7;background:#fff;border-radius:999px;padding:11px 24px;font-family:inherit;font-size:13px;font-weight:700;cursor:pointer;transition:.2s}.add-new-btn:hover{background:#111;color:#fff;border-color:#111}.empty-platform-message{padding:40px;text-align:center;color:#8e8e93;border:1px solid #e8e8e8;border-radius:12px}.modal-overlay{position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;padding:24px;box-sizing:border-box}.nested-overlay{z-index:1100;background:rgba(0,0,0,.32)}.modal-content{width:100%;background:#fff;border-radius:18px;box-shadow:0 24px 70px rgba(0,0,0,.22);box-sizing:border-box}.pattern-modal{max-width:720px;max-height:90vh;overflow:hidden;display:flex;flex-direction:column}.pref-modal{max-width:680px;max-height:85vh;overflow-y:auto;padding:24px}.modal-header{display:flex;align-items:flex-start;justify-content:space-between;padding:28px 30px 20px;border-bottom:1px solid #eee}.modal-header h3{margin:5px 0 3px;font-size:22px;letter-spacing:-.02em}.modal-header p{margin:0;color:#888;font-size:12px}.modal-kicker{font-size:10px;font-weight:800;letter-spacing:.14em;color:#999}.btn-close{width:36px;height:36px;border:0;background:#f3f3f3;border-radius:50%;font-size:22px;line-height:1;cursor:pointer}.modal-form-body{padding:28px 30px;overflow-y:auto}.modal-form-group{margin-bottom:26px}.modal-form-group label{display:flex;align-items:center;font-size:13px;font-weight:700;margin-bottom:9px}.modal-num{width:22px;height:22px;border-radius:50%;background:#111;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:10px;margin-right:9px;flex-shrink:0}.modal-form-row{display:grid;grid-template-columns:1fr 1fr;gap:18px}.modal-input,.modal-select-button,.modal-textarea{width:100%;box-sizing:border-box;border:1px solid #dedede;border-radius:10px;background:#fff;padding:12px 13px;font-family:inherit;font-size:13px;outline:none;color:#111}.modal-input:focus,.modal-select-button:focus,.modal-textarea:focus{border-color:#111}.modal-input:disabled{background:#f4f4f4;color:#aaa}.modal-inline{display:flex;gap:8px}.modal-inline .modal-input:last-child{width:120px}.period-modal-number{flex:1}.budget-input-wrap{display:flex;align-items:center;gap:8px}.budget-input-wrap .modal-input{flex:1;text-align:right}.budget-input-wrap span{font-size:12px;color:#666}.modal-select-button{display:flex;align-items:center;justify-content:space-between;text-align:left;cursor:pointer;min-height:44px}.modal-select-button .placeholder{color:#aaa}.city-input{margin-top:8px}.age-modal-row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px}.age-modal-row>div{display:flex;align-items:center;gap:5px}.age-modal-row .modal-input{min-width:0}.age-modal-row span{font-size:11px;color:#666}.target-section{border:1px solid #eee;border-radius:12px;padding:14px;background:#fafafa}.target-block+.target-block{margin-top:16px}.target-label{display:block;font-size:11px;font-weight:700;color:#666;margin-bottom:7px}.target-options{display:flex;flex-wrap:wrap;gap:6px}.target-chip{border:1px solid #ddd;background:#fff;border-radius:999px;padding:7px 10px;font-family:inherit;font-size:11px;color:#666;cursor:pointer}.target-chip:hover{border-color:#111}.target-chip.selected{background:#111;color:#fff;border-color:#111}.modal-textarea{height:100px;resize:vertical}.pattern-modal-footer{padding:16px 30px 22px;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:10px}.modal-cancel-btn{border:1px solid #ddd;background:#fff;color:#333;border-radius:9px;padding:12px 22px;font-family:inherit;font-weight:600;cursor:pointer}.modal-save-btn{border:1px solid #111;background:#111;color:#fff;border-radius:9px;padding:12px 24px;font-family:inherit;font-weight:700;cursor:pointer;display:inline-flex;gap:18px;align-items:center}.modal-save-btn.small{padding:11px 25px}.modal-cancel-btn:hover{background:#f5f5f5}.modal-toolbar{display:flex;gap:8px;margin:4px 0 14px}.modal-toolbar input{flex:1;min-width:0;padding:11px 12px;border:1px solid #ddd;border-radius:10px;font-family:inherit;outline:none}.modal-toolbar button{border:1px solid #ddd;background:#fff;border-radius:10px;padding:8px 14px;font-family:inherit;cursor:pointer}.modal-selected-header{font-size:12px;font-weight:700;color:#666;margin-bottom:7px}.modal-selected-body{min-height:46px;padding:9px;background:#f7f7f8;border-radius:10px;margin-bottom:18px;display:flex;flex-wrap:wrap;gap:6px;align-items:center}.selected-chip{display:inline-flex;align-items:center;gap:4px;background:rgba(0,100,220,.08);color:#0064dc;border-radius:999px;padding:5px 9px;font-size:11px}.selected-chip button{border:0;background:transparent;color:inherit;cursor:pointer;padding:0}.empty-selected{color:#999;font-size:11px}.pref-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:5px}.pref-option{min-height:40px;border:1px solid #eee;background:#fff;cursor:pointer;font-family:inherit;font-size:11px;border-radius:6px}.pref-option:hover{background:#f7f7f7}.pref-option.selected{background:#111;color:#fff;border-color:#111}.pref-modal .modal-header{padding:0 0 18px;margin-bottom:18px}.pref-modal .modal-footer{margin-top:20px;display:flex;justify-content:flex-end;gap:8px}.fade-slide-enter-active,.fade-slide-leave-active{transition:.35s}.fade-slide-leave-to{opacity:0;transform:translateY(-10px)}.fade-slide-enter-from{opacity:0;transform:translateY(10px)}
@media(max-width:768px){.header{padding:0 1rem}.company-name{font-size:.85rem}.app-title{display:none}.step{padding:0 12px;font-size:.68rem}.content-wrapper{padding:32px 16px 48px}.form-body-vertical{gap:24px;margin-top:24px}.title-area h2,.platform-header h2{font-size:1.75rem}.animated-btn{width:100%;padding:16px 0}.step2-actions{flex-direction:column-reverse}.back-btn{width:100%}.pattern-list-header{display:block}.pattern-list-header p{margin-top:7px}.modal-overlay{padding:10px}.pattern-modal{max-height:94vh}.modal-header{padding:22px 20px 16px}.modal-form-body{padding:22px 20px}.modal-form-row{grid-template-columns:1fr}.age-modal-row{grid-template-columns:1fr}.pattern-modal-footer{padding:14px 20px 18px}.modal-cancel-btn,.modal-save-btn{flex:1;justify-content:center}.pref-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
</style>
