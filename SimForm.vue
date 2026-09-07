<template>
  <div class="sim-form-container">
    <!-- ヘッダー -->
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
    <!-- メインコンテンツ領域 -->
    <main class="content-wrapper">
      <transition name="fade-slide" mode="out-in">
        <!-- STEP 1: ENTRY -->
        <section v-if="currentStep === 1" key="step1" class="step-panel step1-content">
          <div class="title-area">
            <h2>ENTRY</h2>
            <span class="required-label">*必須入力</span>
          </div>
          <div class="form-body-vertical">
            <!-- ❶ 依頼者名 -->
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(1, formData.applicantName) }">
              <label class="unified-font"><span class="num">1</span> <span class="req">*</span>依頼者名</label>
              <input
                type="text"
                ref="applicantNameInput"
                class="unified-font input-ime-active transparent-input"
                @focus="activeFieldIndex = 1"
                v-model="formData.applicantName"
                placeholder="例：山田 太郎"
              />
            </div>
            <!-- ❷ 所属 -->
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(2, formData.department) }">
              <label class="unified-font"><span class="num">2</span> 所属</label>
              <div class="radio-row">
                <label
                  v-for="option in departmentOptions"
                  :key="option.value"
                  class="radio-item"
                  :class="{ 'is-selected': formData.department === option.value }"
                >
                  <input
                    type="radio"
                    name="department"
                    :value="option.value"
                    v-model="formData.department"
                    @focus="activeFieldIndex = 2"
                  />
                  <span class="custom-radio"></span>
                  <span class="radio-label-text unified-font">{{ option.label }}</span>
                </label>
              </div>
            </div>
            <!-- ❸ Email -->
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(3, formData.email) }">
              <label class="unified-font"><span class="num">3</span> Email</label>
              <input
                type="email"
                class="unified-font input-ime-inactive transparent-input"
                v-model="formData.email"
                @focus="activeFieldIndex = 3"
                @blur="touched.email = true"
                :class="{ 'is-error': isEmailInvalid }"
                placeholder="example@domain.com"
                autocomplete="off"
              />
              <p v-if="isEmailInvalid" class="error-text">有効なメールアドレスを入力してください</p>
            </div>
            <!-- ❹ クライアント名 -->
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(4, formData.clientName) }">
              <label class="unified-font"><span class="num">4</span> <span class="req">*</span>クライアント名</label>
              <input
                type="text"
                class="unified-font input-ime-active transparent-input"
                v-model="formData.clientName"
                @focus="activeFieldIndex = 4"
                placeholder="例：株式会社Hakuhodo DY ONE"
              />
            </div>
            <!-- ❺ 案件名 -->
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(5, formData.projectName) }">
              <label class="unified-font"><span class="num">5</span> <span class="req">*</span>案件名</label>
              <input
                type="text"
                class="unified-font input-ime-active transparent-input"
                v-model="formData.projectName"
                @focus="activeFieldIndex = 5"
                placeholder="入力してください"
              />
            </div>
            <!-- ❻ 希望納期 -->
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(6, formData.dueDate) }">
              <label class="unified-font"><span class="num">6</span> 希望納期</label>
              <input
                type="date"
                class="unified-font custom-date-input transparent-input"
                v-model="formData.dueDate"
                @focus="activeFieldIndex = 6"
              />
            </div>
            <!-- ❼ 備考 -->
            <div class="form-group" :class="{ 'is-dimmed': isFieldDimmed(7, formData.remarks) }">
              <label class="unified-font"><span class="num">7</span> 備考</label>
              <textarea
                class="unified-font custom-textarea input-ime-active transparent-input"
                v-model="formData.remarks"
                @focus="activeFieldIndex = 7"
                placeholder="補足事項があれば入力してください"
              ></textarea>
            </div>
          </div>
          <div class="action-area">
            <button
              class="next-btn animated-btn"
              :class="{ visible: isStep1Valid }"
              :disabled="!isStep1Valid"
              @click="currentStep = 2"
            >
              <span>PLATFORMS 選択へ進む</span>
              <span class="btn-arrow">&rarr;</span>
            </button>
          </div>
        </section>
        <!-- STEP 2: PLATFORMS -->
        <section v-else-if="currentStep === 2" key="step2" class="step-panel step2-content">
          <div class="platform-header">
            <h2>PLATFORMS</h2>
            <span class="sub-label">依頼するプラットフォームを選択し、マージンを設定してください</span>
          </div>
          <div class="platform-grid-container">
            <div class="platform-label-column">
              <div class="label-header">媒体</div>
              <div class="label-cell">種別</div>
              <div class="label-cell stripe">マージン</div>
            </div>
            <div class="platform-columns-wrapper">
              <div
                v-for="platform in platformList"
                :key="platform.id"
                class="platform-column"
              >
                <div class="chip-cell">
                  <div
                    class="platform-chip"
                    :class="{ 'selected': platforms[platform.id].selected }"
                    @click="togglePlatform(platform.id)"
                    @mouseenter="onMouseEnter(platform.id)"
                    @mouseleave="onMouseLeave(platform.id)"
                  >
                    <div class="chip-logos">
                      <div
                        v-for="(logo, idx) in platform.logos"
                        :key="idx"
                        class="lottie-wrapper"
                        :class="[logo.class, { 'is-active': hoveredPlatform === platform.id || platforms[platform.id].selected }]"
                      >
                        <DotLottieVue
                          :src="logo.lottiePath"
                          :loop="true"
                          :ref="(el) => setLottieRef(el, platform.id)"
                          class="lottie-player"
                        />
                      </div>
                    </div>
                    <span class="platform-chip-name">{{ platform.name }}</span>
                  </div>
                </div>
                <div class="grid-cell">
                  <select
                    v-model="platforms[platform.id].marginType"
                    class="margin-select-line"
                    :disabled="!platforms[platform.id].selected"
                  >
                    <option value="percent">% (グロス)</option>
                    <option value="fixed">定額</option>
                  </select>
                </div>
                <div class="grid-cell stripe">
                  <div class="margin-input-wrap-inline">
                    <input
                      type="number"
                      v-model.number="platforms[platform.id].marginValue"
                      class="margin-input-line transparent-input"
                      placeholder="20"
                      :disabled="!platforms[platform.id].selected"
                    />
                    <span class="margin-suffix-text">
                      {{ platforms[platform.id].marginType === 'fixed' ? '円' : '%' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="action-area step2-actions">
            <button class="back-btn" @click="currentStep = 1">&larr; ENTRYに戻る</button>
            <button
              class="next-btn animated-btn visible"
              :disabled="!isStep2Valid"
              @click="currentStep = 3"
            >
              <span>FORM 入力へ進む</span>
              <span class="btn-arrow">&rarr;</span>
            </button>
          </div>
        </section>
        <!-- STEP 3: FORM DETAILS (YouTube) -->
        <section v-else-if="currentStep === 3" key="step3" class="step-panel step3-content">
          <div class="platform-header">
            <h2>FORM DETAILS</h2>
            <span class="sub-label">YouTubeの詳細パラメータを設定してください</span>
          </div>
          <div v-if="platforms.youtube.selected" class="youtube-form-container">
            <div class="dark-card-wrapper">
              <div class="table-scroll-container">
                <table class="pattern-matrix-table">
                  <colgroup>
                    <col style="width:72px"><col style="width:32px"><col style="width:150px">
                    <col style="width:240px"><col style="width:190px"><col style="width:190px">
                    <col style="width:160px"><col style="width:140px"><col style="width:160px">
                    <col style="width:180px"><col style="width:90px"><col style="width:110px">
                    <col style="width:110px"><col style="width:110px"><col style="width:240px">
                    <col style="width:240px"><col style="width:240px"><col style="width:120px"><col style="width:450px">
                  </colgroup>
                  <thead>
                    <tr>
                      <th>操作</th><th>#</th><th>メニュー</th><th>配信面</th><th>縦型</th><th>横型</th>
                      <th>配信期間</th><th>予算</th><th>都道府県</th><th>市町村</th><th>性別</th>
                      <th>年齢①</th><th>年齢②</th><th>年齢③</th><th>ターゲット①</th><th>ターゲット②</th>
                      <th>ターゲット③</th><th>デバイス</th><th>備考</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(pattern,index) in youtubePatterns" :key="pattern.id">
                      <td class="action-cell">
                        <div class="action-col-inner">
                          <button type="button" class="del-half-btn" :disabled="youtubePatterns.length <= 1" @click="removeYoutubePattern(index)" aria-label="削除">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                          </button>
                          <button type="button" class="copy-half-btn" @click="copyYoutubePattern(index)" aria-label="複製">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M6 15H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1"/></svg>
                          </button>
                        </div>
                      </td>
                      <td class="text-center font-bold">{{ index + 1 }}</td>
                      <td><select v-model="pattern.menu" class="matrix-select"><option value="">選択</option><option v-for="v in youtubeMenus" :key="v" :value="v">{{ v }}</option></select></td>
                      <td><select v-model="pattern.placement" class="matrix-select"><option value="">選択</option><option v-for="v in youtubePlacements" :key="v" :value="v">{{ v }}</option></select></td>
                      <td><select v-model="pattern.verticalCreative" class="matrix-select"><option value="">選択</option><option v-for="v in verticalCreativeOptions" :key="v" :value="v">{{ v }}</option></select></td>
                      <td><select v-model="pattern.horizontalCreative" class="matrix-select"><option value="">選択</option><option v-for="v in horizontalCreativeOptions" :key="v" :value="v">{{ v }}</option></select></td>
                      <td>
                        <div class="period-wrap">
                          <input v-model.number="pattern.periodNumber" type="number" min="1" class="matrix-input period-number" placeholder="数値">
                          <select v-model="pattern.periodUnit" class="matrix-select period-unit"><option value="日">日</option><option value="週">週</option><option value="月">月</option></select>
                        </div>
                      </td>
                      <td><input v-model.number="pattern.budget" type="number" min="0" class="matrix-input text-right" placeholder="1000000"></td>
                      <td>
                        <button type="button" class="pref-box" @click="openPrefModal(index)">
                          <span v-if="pattern.prefNames.length" class="pref-lines"><span v-for="name in pattern.prefNames" :key="name" class="pref-line">{{ name }}</span></span>
                          <span v-else class="pref-placeholder">エリア選択</span>
                        </button>
                      </td>
                      <td><input v-model="pattern.city" type="text" class="matrix-input" :disabled="pattern.prefNames.includes('全国')" placeholder="市区町村"></td>
                      <td><select v-model="pattern.gender" class="matrix-select"><option value="all">すべて</option><option value="male">男性</option><option value="female">女性</option></select></td>
                      <td><div class="age-wrap"><input v-model="pattern.age1" type="number" min="0" max="100" class="matrix-input age-num"><span>歳</span></div></td>
                      <td><div class="age-wrap"><input v-model="pattern.age2" type="number" min="0" max="100" class="matrix-input age-num"><span>歳</span></div></td>
                      <td><div class="age-wrap"><input v-model="pattern.age3" type="number" min="0" max="100" class="matrix-input age-num"><span>歳</span></div></td>
                      <td><button type="button" class="target-trigger" @click="openTargetModal(index,1)"><span v-if="pattern.target1.length" class="selected-display"><span v-for="v in pattern.target1" :key="v">{{ v }}</span></span><span v-else>選択</span></button></td>
                      <td><button type="button" class="target-trigger" @click="openTargetModal(index,2)"><span v-if="pattern.target2.length" class="selected-display"><span v-for="v in pattern.target2" :key="v">{{ v }}</span></span><span v-else>選択</span></button></td>
                      <td><button type="button" class="target-trigger" @click="openTargetModal(index,3)"><span v-if="pattern.target3.length" class="selected-display"><span v-for="v in pattern.target3" :key="v">{{ v }}</span></span><span v-else>選択</span></button></td>
                      <td><select v-model="pattern.device" class="matrix-select"><option value="all">すべて</option><option value="mobile">モバイル</option><option value="desktop">PC</option><option value="tv">コネクテッドTV</option></select></td>
                      <td><textarea v-model="pattern.notes" class="matrix-input notes-input" rows="2" placeholder="備考"></textarea></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="add-pattern-area">
                <button type="button" class="add-new-btn" @click="addYoutubePattern">＋ New</button>
              </div>
            </div>
          </div>
          <div v-else class="empty-platform-message">PLATFORMSでYouTubeを選択してください。</div>
          <div class="action-area step2-actions">
            <button class="back-btn" @click="currentStep = 2">&larr; PLATFORMSに戻る</button>
            <button class="next-btn animated-btn visible" @click="submitForm"><span>送信する</span><span class="btn-arrow">&rarr;</span></button>
          </div>
        </section>
      </transition>
      <!-- 都道府県モーダル -->
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closePrefModal">
        <div class="modal-content youtube-modal-content">
          <div class="modal-header"><h3>都道府県を選択</h3><button type="button" class="btn-close" @click="closePrefModal">×</button></div>
          <div class="modal-toolbar"><input v-model="prefSearch" type="search" placeholder="都道府県を検索"><button type="button" @click="clearPrefSelection">クリア</button></div>
          <div class="modal-selected-header">選択済み</div>
          <div class="modal-selected-body"><span v-for="name in selectedPrefNames" :key="name" class="selected-chip">{{ name }} <button type="button" @click="removePref(name)">×</button></span><span v-if="!selectedPrefNames.length" class="empty-selected">未選択</span></div>
          <div class="modal-options-header">都道府県</div>
          <div class="pref-grid">
            <button
              v-for="pref in filteredPrefectures"
              :key="pref.id"
              type="button"
              class="pref-option"
              :class="{ selected: selectedPrefNames.includes(pref.name) }"
              @click="togglePref(pref)"
            >
              {{ pref.name }}
            </button>
          </div>
          <div class="modal-footer">
            <button type="button" class="back-btn" @click="closePrefModal">キャンセル</button>
            <button type="button" class="back-btn modal-confirm-btn" @click="confirmPrefSelection">確定</button>
          </div>
        </div>
      </div>
      <!-- ターゲットモーダル -->
      <div v-if="targetModalOpen" class="modal-overlay" @click.self="closeTargetModal">
        <div class="modal-content youtube-modal-content">
          <div class="modal-header"><h3>{{ targetModalTitle }}</h3><button type="button" class="btn-close" @click="closeTargetModal">×</button></div>
          <div class="modal-toolbar"><input v-model="targetSearch" type="search" placeholder="ターゲットを検索"><button type="button" @click="clearTargetSelection">クリア</button></div>
          <div class="modal-selected-header">選択済み</div>
          <div class="modal-selected-body"><span v-for="value in selectedTargetValues" :key="value" class="selected-chip">{{ value }} <button type="button" @click="removeTarget(value)">×</button></span><span v-if="!selectedTargetValues.length" class="empty-selected">未選択</span></div>
          <div class="modal-options-header">候補</div>
          <div class="target-option-list">
            <button
              v-for="value in filteredTargetOptions"
              :key="value"
              type="button"
              class="target-option"
              :class="{ selected: selectedTargetValues.includes(value) }"
              @click="toggleTarget(value)"
            >
              {{ value }}
            </button>
          </div>
          <div class="modal-footer">
            <button type="button" class="back-btn" @click="closeTargetModal">キャンセル</button>
            <button type="button" class="back-btn modal-confirm-btn" @click="confirmTargetSelection">確定</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, computed, ref, onMounted } from 'vue'
import { DotLottieVue } from '@lottiefiles/dotlottie-vue'

const currentStep = ref(1)
const activeFieldIndex = ref(1)
const hoveredPlatform = ref(null)
const applicantNameInput = ref(null)
const lottieRefs = reactive({})

onMounted(() => {
  if (applicantNameInput.value) {
    applicantNameInput.value.focus()
  }
})

// ENTRYデータ
const formData = reactive({
  applicantName: '',
  department: '',
  email: '',
  clientName: '',
  projectName: '',
  dueDate: '',
  remarks: ''
})

/* STEP 3: パターン設定データ */
let youtubePatternSeq = 1

const createEmptyPattern = () => ({
  id: `yt-${Date.now()}-${youtubePatternSeq++}`,
  menu: '',
  placement: '',
  verticalCreative: '',
  horizontalCreative: '',
  periodNumber: null,
  periodUnit: '日',
  budget: null,
  prefId: '',
  prefName: '',
  prefNames: [],
  city: '',
  gender: 'all',
  age1: '',
  age2: '',
  age3: '',
  target1: [],
  target2: [],
  target3: [],
  device: 'all',
  notes: ''
})

const youtubePatterns = reactive([createEmptyPattern()])

const youtubeMenus = [
  'VRC2.0（リーチ）',
  'VVC（視聴）',
  'スキップ不可',
  '目標FQ（マルチフォーマット）',
  '目標FQ（スキップ可）',
  '目標FQ（スキップ不可）'
]

const placementOptionsByMenu = {
  'VRC2.0（リーチ）': [
    'インストリーム',
    'バンパー',
    'インフィード',
    'ショート'
  ],
  'VVC（視聴）': [
    'インストリーム',
    'インフィード',
    'ショート'
  ],
  'スキップ不可': [
    'インストリーム'
  ],
  '目標FQ（マルチフォーマット）': [
    'インストリーム',
    'バンパー',
    'インフィード',
    'ショート'
  ],
  '目標FQ（スキップ可）': [
    'インストリーム',
    'バンパー'
  ],
  '目標FQ（スキップ不可）': [
    'インストリーム'
  ]
}

const videoLengthOptions = [
  { value: 'なし', text: 'なし' },
  { value: '6s＊バンパー', text: '6s＊バンパー' },
  { value: '15s', text: '15s' },
  { value: '30s', text: '30s' },
  { value: '45s', text: '45s' },
  { value: '60s', text: '60s' },
  { value: '90s', text: '90s' },
  { value: '120s', text: '120s' }
]

const restrictedVideoLengthOptions = [
  { value: 'なし', text: 'なし' },
  { value: '15s', text: '15s' },
  { value: '30s ※CTVのみ', text: '30s ※CTVのみ' },
  { value: '6sバンパー+15s ※β版', text: '6sバンパー+15s ※β版' }
]

const prefList = [
  { id: 'national', name: '全国' },
  { id: 'hokkaido', name: '北海道' },
  { id: 'aomori', name: '青森県' },
  { id: 'iwate', name: '岩手県' },
  { id: 'miyagi', name: '宮城県' },
  { id: 'akita', name: '秋田県' },
  { id: 'yamagata', name: '山形県' },
  { id: 'fukushima', name: '福島県' },
  { id: 'ibaraki', name: '茨城県' },
  { id: 'tochigi', name: '栃木県' },
  { id: 'gunma', name: '群馬県' },
  { id: 'saitama', name: '埼玉県' },
  { id: 'chiba', name: '千葉県' },
  { id: 'tokyo', name: '東京都' },
  { id: 'kanagawa', name: '神奈川県' },
  { id: 'niigata', name: '新潟県' },
  { id: 'toyama', name: '富山県' },
  { id: 'ishikawa', name: '石川県' },
  { id: 'fukui', name: '福井県' },
  { id: 'yamanashi', name: '山梨県' },
  { id: 'nagano', name: '長野県' },
  { id: 'gifu', name: '岐阜県' },
  { id: 'shizuoka', name: '静岡県' },
  { id: 'aichi', name: '愛知県' },
  { id: 'mie', name: '三重県' },
  { id: 'shiga', name: '滋賀県' },
  { id: 'kyoto', name: '京都府' },
  { id: 'osaka', name: '大阪府' },
  { id: 'hyogo', name: '兵庫県' },
  { id: 'nara', name: '奈良県' },
  { id: 'wakayama', name: '和歌山県' },
  { id: 'tottori', name: '鳥取県' },
  { id: 'shimane', name: '島根県' },
  { id: 'okayama', name: '岡山県' },
  { id: 'hiroshima', name: '広島県' },
  { id: 'yamaguchi', name: '山口県' },
  { id: 'tokushima', name: '徳島県' },
  { id: 'kagawa', name: '香川県' },
  { id: 'ehime', name: '愛媛県' },
  { id: 'kochi', name: '高知県' },
  { id: 'fukuoka', name: '福岡県' },
  { id: 'saga', name: '佐賀県' },
  { id: 'nagasaki', name: '長崎県' },
  { id: 'kumamoto', name: '熊本県' },
  { id: 'oita', name: '大分県' },
  { id: 'miyazaki', name: '宮崎県' },
  { id: 'kagoshima', name: '鹿児島県' },
  { id: 'okinawa', name: '沖縄県' }
]

const isModalOpen = ref(false)
const selectedRowIndex = ref(null)
const prefSearch = ref('')
const pendingPrefNames = ref([])

const filteredPrefList = computed(() => {
  const q = prefSearch.value.trim().toLowerCase()
  return q
    ? prefList.filter(pref => pref.name.toLowerCase().includes(q))
    : prefList
})

const selectedPrefNames = computed(() => pendingPrefNames.value)

const openPrefModal = (rowIndex) => {
  selectedRowIndex.value = rowIndex
  pendingPrefNames.value = [...youtubePatterns[rowIndex].prefNames]
  prefSearch.value = ''
  isModalOpen.value = true
}

const closePrefModal = () => {
  isModalOpen.value = false
  selectedRowIndex.value = null
  prefSearch.value = ''
}

const clearPrefSelection = () => {
  pendingPrefNames.value = []
}

const removePref = (name) => {
  pendingPrefNames.value = pendingPrefNames.value.filter(
    value => value !== name
  )
}

const confirmPrefSelection = () => {
  if (selectedRowIndex.value === null) return

  const row = youtubePatterns[selectedRowIndex.value]

  row.prefNames = [...pendingPrefNames.value]
  row.prefId = row.prefNames[0] || ''
  row.prefName = row.prefNames.join(' / ')

  if (row.prefNames.includes('全国')) {
    row.city = ''
  }

  closePrefModal()
}

const targetLists = {
  1: [
    'アフィニティカテゴリ',
    'スポーツファン',
    '旅行好き',
    'テクノロジー愛好者',
    '美容・ファッション',
    'グルメ・料理',
    '自動車愛好者',
    'ゲーム愛好者',
    '音楽ファン',
    'ニュース好き',
    'アウトドア好き'
  ],
  2: [
    '購買意向の強いオーディエンス',
    '家電・電子機器',
    '自動車',
    '旅行',
    '金融サービス',
    '不動産',
    '求人・転職',
    '教育',
    '美容商品',
    '通信サービス',
    'ソフトウェア'
  ],
  3: [
    '詳細なユーザー属性',
    '子どもあり',
    '大学生',
    '会社員',
    '経営者',
    '住宅所有者',
    '最近結婚した',
    '最近引っ越した',
    '親',
    '世帯収入上位層'
  ]
}

const targetModalOpen = ref(false)
const targetRowIndex = ref(null)
const targetModalColumn = ref(1)
const targetSearch = ref('')
const pendingTargetNames = ref([])

const targetModalTitle = computed(() => {
  const labels = ['①', '②', '③']
  return `ターゲット${labels[targetModalColumn.value - 1]}を選択`
})

const filteredTargetOptions = computed(() => {
  const q = targetSearch.value.trim().toLowerCase()
  const list = targetLists[targetModalColumn.value] || []

  return q
    ? list.filter(value => value.toLowerCase().includes(q))
    : list
})

const openTargetModal = (rowIndex, column) => {
  targetRowIndex.value = rowIndex
  targetModalColumn.value = column
  pendingTargetNames.value = [
    ...youtubePatterns[rowIndex][`target${column}`]
  ]
  targetSearch.value = ''
  targetModalOpen.value = true
}

const closeTargetModal = () => {
  targetModalOpen.value = false
  targetRowIndex.value = null
  targetSearch.value = ''
}

const clearTargetSelection = () => {
  pendingTargetNames.value = []
}

const removeTarget = (name) => {
  pendingTargetNames.value = pendingTargetNames.value.filter(
    value => value !== name
  )
}

const confirmTargetSelection = () => {
  if (targetRowIndex.value === null) return

  youtubePatterns[targetRowIndex.value][
    `target${targetModalColumn.value}`
  ] = [...pendingTargetNames.value]

  closeTargetModal()
}

const addYoutubePattern = () => {
  youtubePatterns.push(createEmptyPattern())
}

const removeYoutubePattern = (index) => {
  if (youtubePatterns.length > 1) {
    youtubePatterns.splice(index, 1)
  }
}

const copyYoutubePattern = (index) => {
  const source = youtubePatterns[index]
  const copy = JSON.parse(JSON.stringify(source))

  copy.id = `yt-${Date.now()}-${youtubePatternSeq++}`

  youtubePatterns.splice(index + 1, 0, copy)
}

const touched = reactive({
  email: false
})

const departmentOptions = [
  { value: 'dept1', label: '第1営業部' },
  { value: 'dept2', label: '第2営業部' },
  { value: 'dev', label: '開発部' }
]

const isFieldDimmed = (index, value) => {
  const isEmpty =
    !value ||
    (typeof value === 'string' && value.trim() === '')

  const isNotActive = activeFieldIndex.value !== index

  return isNotActive && isEmpty
}

const isEmailInvalid = computed(() => {
  if (!formData.email.trim()) return false

  const emailPattern =
    /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/

  return (
    touched.email &&
    !emailPattern.test(formData.email.trim())
  )
})

const isStep1Valid = computed(() => {
  return (
    formData.applicantName.trim() !== '' &&
    formData.clientName.trim() !== '' &&
    formData.projectName.trim() !== '' &&
    !isEmailInvalid.value
  )
})

const platformList = [
  {
    id: 'meta',
    name: 'META',
    logos: [
      {
        lottiePath: '/images_json/Meta.lottie',
        class: 'meta'
      }
    ]
  },
  {
    id: 'listing',
    name: 'LISTING',
    logos: [
      {
        lottiePath: '/images_json/Listing.lottie',
        class: 'listing'
      }
    ]
  },
  {
    id: 'youtube',
    name: 'YOUTUBE',
    logos: [
      {
        lottiePath: '/images_json/YouTube.lottie',
        class: 'youtube'
      }
    ]
  }
]

const platforms = reactive(
  platformList.reduce((acc, platform) => {
    acc[platform.id] = {
      selected: false,
      marginType: 'percent',
      marginValue: 20
    }

    return acc
  }, {})
)

const setLottieRef = (el, id) => {
  if (el) {
    lottieRefs[id] = el
  }
}

const getDotLottie = (id) => {
  const lottieComp = lottieRefs[id]

  if (!lottieComp) return null

  return lottieComp.getDotLottieInstance
    ? lottieComp.getDotLottieInstance()
    : null
}

const onMouseEnter = (id) => {
  hoveredPlatform.value = id

  const dotLottie = getDotLottie(id)

  if (dotLottie) {
    dotLottie.play()
  }
}

const onMouseLeave = (id) => {
  hoveredPlatform.value = null

  if (!platforms[id].selected) {
    const dotLottie = getDotLottie(id)

    if (dotLottie) {
      dotLottie.stop()
    }
  }
}

const togglePlatform = (id) => {
  platforms[id].selected = !platforms[id].selected

  const dotLottie = getDotLottie(id)

  if (!dotLottie) return

  if (platforms[id].selected) {
    dotLottie.play()
  } else if (hoveredPlatform.value !== id) {
    dotLottie.stop()
  }
}

const isStep2Valid = computed(() => {
  return Object.values(platforms).some(
    platform => platform.selected
  )
})

const submitForm = () => {
  const payload = {
    entry: formData,
    platforms,
    youtube: {
      patterns: youtubePatterns
    }
  }

  console.log('送信データ:', payload)
  alert('送信が完了しました！')
}
</script>

<style scoped>
/* 基本設定 */
.sim-form-container {
  font-family: "Barlow", "Noto Sans JP", -apple-system, BlinkMacSystemFont, sans-serif;
  color: #111111;
  background-color: #ffffff;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  box-sizing: border-box;
}

.transparent-input,
.transparent-input:-webkit-autofill,
.transparent-input:-webkit-autofill:hover,
.transparent-input:-webkit-autofill:focus,
.transparent-input:-webkit-autofill:active {
  background-color: transparent !important;
  -webkit-text-fill-color: #111111 !important;
  transition: background-color 5000s ease-in-out 0s;
}

/* ヘッダー */
.header {
  position: sticky;
  top: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #f0f0f0;
  padding: 0 1.5rem 0 2.5rem;
  flex-shrink: 0;
  z-index: 100;
  background-color: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  height: 64px;
}

.logo-area {
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 700;
}

.company-name {
  font-size: 1rem;
  letter-spacing: -0.02em;
}

.app-title {
  font-size: 1rem;
  color: #888888;
  letter-spacing: 0.05em;
  font-weight: 400;
}

.stepper {
  display: flex;
  height: 100%;
}

.step {
  padding: 0 24px;
  display: flex;
  align-items: center;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  color: #aaaaaa;
  transition: all 0.3s ease;
}

.step.active {
  background-color: #111111;
  color: #ffffff;
}

/* メイン領域 */
.content-wrapper {
  flex: 1;
  width: 100%;
  display: flex;
  justify-content: center;
  padding: 48px 20px 96px;
  box-sizing: border-box;
}

.step-panel {
  width: 100%;
  box-sizing: border-box;
}

.step1-content {
  max-width: 680px;
}

.step2-content,
.step3-content {
  max-width: 1400px;
}

.title-area h2,
.platform-header h2 {
  font-size: 2.2rem;
  font-weight: 800;
  margin: 0;
  letter-spacing: -0.03em;
}

.required-label,
.sub-label {
  font-size: 0.75rem;
  color: #888888;
  display: block;
  margin-top: 6px;
}

/* STEP 1 スタイル */
.form-body-vertical {
  margin-top: 40px;
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.form-group {
  opacity: 1;
  transition: opacity 0.35s ease;
}

.form-group.is-dimmed {
  opacity: 0.25;
}

.form-group label {
  display: flex;
  align-items: center;
  font-weight: 600;
  margin-bottom: 10px;
  color: #111111;
  white-space: nowrap;
}

.num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  background-color: #111111;
  color: #ffffff;
  border-radius: 50%;
  font-size: 0.72rem;
  font-weight: 700;
  margin-right: 10px;
  flex-shrink: 0;
}

.req {
  color: #e03131;
  margin-right: 4px;
}

.unified-font {
  font-size: 0.95rem !important;
  line-height: 1.5;
}

.form-group input[type="text"],
.form-group input[type="email"],
.custom-date-input {
  width: 100%;
  padding: 12px 0;
  border: none;
  border-bottom: 1px solid #e0e0e0;
  color: #111111;
  outline: none;
  transition: border-color 0.3s ease;
  box-sizing: border-box;
}

.custom-textarea {
  width: 100%;
  height: 120px;
  padding: 12px 0;
  border: none;
  border-bottom: 1px solid #e0e0e0;
  border-radius: 0;
  color: #111111;
  outline: none;
  resize: vertical;
  font-family: inherit;
  box-sizing: border-box;
}

.custom-textarea:focus,
.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus,
.custom-date-input:focus {
  border-bottom: 2px solid #111111;
}

.error-text {
  color: #e03131;
  font-size: 0.75rem;
  margin-top: 6px;
}

.radio-row {
  display: flex;
  gap: 12px;
  margin-top: 6px;
  flex-wrap: wrap;
}

.radio-item {
  flex: 1;
  min-width: 100px;
  display: flex;
  align-items: center;
  padding: 10px 8px;
  border-radius: 6px;
  cursor: pointer;
}

.radio-item input[type="radio"] {
  position: absolute;
  opacity: 0;
}

.custom-radio {
  width: 14px;
  height: 14px;
  border: 2px solid #cccccc;
  border-radius: 50%;
  margin-right: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.custom-radio::after {
  content: '';
  width: 6px;
  height: 6px;
  background-color: #111111;
  border-radius: 50%;
  opacity: 0;
  transform: scale(0.5);
}

.radio-item.is-selected .custom-radio {
  border-color: #111111;
}

.radio-item.is-selected .custom-radio::after {
  opacity: 1;
  transform: scale(1);
}

.radio-label-text {
  font-size: 0.9rem;
}

.action-area {
  margin-top: 48px;
  display: flex;
  justify-content: flex-end;
}

.next-btn,
.back-btn {
  border: none;
  cursor: pointer;
  font-family: inherit;
}

.next-btn {
  display: inline-flex;
  align-items: center;
  gap: 16px;
  background-color: #111111;
  color: #ffffff;
  padding: 14px 22px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
  transition: all 0.25s ease;
}

.next-btn:disabled {
  opacity: 0.25;
  cursor: not-allowed;
}

.next-btn:not(:disabled):hover {
  transform: translateX(4px);
}

.btn-arrow {
  font-size: 1.1rem;
}

.back-btn {
  background: transparent;
  color: #666666;
  padding: 14px 18px;
  font-size: 0.85rem;
  font-weight: 600;
  border-radius: 8px;
}

.back-btn:hover {
  background-color: #f5f5f5;
}

.step2-actions {
  justify-content: space-between;
  align-items: center;
}

/* STEP 2 */
.platform-header {
  margin-bottom: 40px;
}

.platform-grid-container {
  display: flex;
  width: 100%;
  border-top: 1px solid #e8e8e8;
  border-left: 1px solid #e8e8e8;
}

.platform-label-column {
  width: 100px;
  flex-shrink: 0;
}

.label-header,
.label-cell {
  display: flex;
  align-items: center;
  justify-content: center;
  border-right: 1px solid #e8e8e8;
  border-bottom: 1px solid #e8e8e8;
  font-size: 0.75rem;
  font-weight: 700;
}

.label-header {
  height: 110px;
  background-color: #111111;
  color: #ffffff;
}

.label-cell {
  height: 82px;
  color: #666666;
  background-color: #fafafa;
}

.label-cell.stripe {
  background-color: #f5f5f5;
}

.platform-columns-wrapper {
  display: flex;
  flex: 1;
}

.platform-column {
  flex: 1;
  min-width: 0;
}

.chip-cell,
.grid-cell {
  border-right: 1px solid #e8e8e8;
  border-bottom: 1px solid #e8e8e8;
}

.chip-cell {
  height: 110px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px;
  box-sizing: border-box;
}

.platform-chip {
  width: 100%;
  height: 72px;
  border: 1px solid #e1e1e1;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  background: #ffffff;
  transition: all 0.25s ease;
}

.platform-chip:hover {
  border-color: #111111;
  transform: translateY(-2px);
}

.platform-chip.selected {
  background-color: #111111;
  color: #ffffff;
  border-color: #111111;
}

.chip-logos {
  display: flex;
  align-items: center;
  justify-content: center;
}

.lottie-wrapper {
  width: 30px;
  height: 30px;
  overflow: hidden;
}

.lottie-player {
  width: 100%;
  height: 100%;
}

.platform-chip-name {
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.08em;
}

.grid-cell {
  height: 82px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  box-sizing: border-box;
}

.grid-cell.stripe {
  background-color: #fafafa;
}

.margin-select-line {
  width: 100%;
  border: none;
  background: transparent;
  outline: none;
  font-family: inherit;
  font-size: 0.8rem;
  color: #111111;
  text-align: center;
}

.margin-select-line:disabled,
.margin-input-line:disabled {
  color: #cccccc;
  cursor: not-allowed;
}

.margin-input-wrap-inline {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.margin-input-line {
  width: 70px;
  border: none;
  border-bottom: 1px solid #d8d8d8;
  padding: 8px 4px;
  outline: none;
  text-align: right;
  background: transparent;
  font-family: inherit;
}

.margin-suffix-text {
  font-size: 0.8rem;
  margin-left: 4px;
  color: #666666;
}

/* STEP 3 */
.youtube-form-container {
  width: 100%;
}

.dark-card-wrapper {
  width: 100%;
  overflow: hidden;
}

.table-scroll-container {
  width: 100%;
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
}

.pattern-matrix-table {
  width: 3393px !important;
  min-width: 3393px !important;
  table-layout: fixed;
  border-collapse: collapse;
  background: #ffffff;
}

.pattern-matrix-table th {
  height: 48px;
  padding: 8px;
  background: linear-gradient(180deg, #222222 0%, #111111 100%);
  color: #ffffff;
  border-right: 1px solid #3d3d3d;
  border-bottom: 1px solid #111111;
  font-size: 11px;
  font-weight: 700;
  white-space: nowrap;
  text-align: center;
}

.pattern-matrix-table td {
  position: relative;
  height: 96px;
  padding: 10px;
  vertical-align: middle;
  border-right: 1px solid #e2e2e2;
  border-bottom: 1px solid #e2e2e2;
  background: rgba(255, 255, 255, 0.96);
  font-size: 12px;
}

.pattern-matrix-table tbody tr:nth-child(even) td {
  background: #fafafa;
}

.text-center {
  text-align: center;
}

.font-bold {
  font-weight: 700;
}

.text-right {
  text-align: right;
}

.matrix-input,
.matrix-select,
.pref-box,
.target-trigger {
  width: 100%;
  min-height: 36px;
  box-sizing: border-box;
}

.matrix-input,
.matrix-select {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 10px;
  padding: 8px 10px;
  font-size: 12px;
  font-family: inherit;
  outline: none;
}

.matrix-input:focus,
.matrix-select:focus,
.pref-box:focus,
.target-trigger:focus {
  border-color: #111111;
}

.matrix-input:disabled {
  background: #f2f2f7;
  color: rgba(0, 0, 0, 0.3);
  cursor: not-allowed;
}

.matrix-select {
  appearance: auto;
}

.action-cell {
  padding: 0 !important;
}

.action-col-inner {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
}

.del-half-btn,
.copy-half-btn {
  flex: 1;
  border: 0;
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.del-half-btn {
  color: #c94b4b;
  border-bottom: 1px solid rgba(0, 0, 0, 0.07);
}

.del-half-btn:disabled {
  opacity: 0.25;
  cursor: not-allowed;
}

.del-half-btn svg,
.copy-half-btn svg {
  width: 22px;
  height: 22px;
}

.copy-half-btn {
  color: #555555;
}

.period-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.period-number {
  width: 58px !important;
  flex: 0 0 58px;
  text-align: center;
}

.period-unit {
  width: 72px !important;
  flex: 0 0 72px;
}

.age-wrap {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  width: 100%;
}

.age-num {
  width: 62px !important;
  text-align: center;
}

.pref-box {
  text-align: left;
  cursor: pointer;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 10px;
  padding: 8px 10px;
}

.pref-lines {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.pref-line {
  display: block;
  font-size: 12px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.pref-placeholder {
  color: rgba(60, 60, 67, 0.35);
}

.target-trigger {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 10px;
  padding: 8px 10px;
  text-align: left;
  cursor: pointer;
  color: #8e8e93;
}

.target-trigger .selected-display {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.target-trigger .selected-display span {
  color: rgb(0, 100, 220);
  font-size: 10px;
  line-height: 1.25;
}

.notes-input {
  resize: vertical;
  min-height: 54px;
}

.add-pattern-area {
  display: flex;
  justify-content: center;
  padding: 16px;
}

.add-new-btn {
  border: 1px solid #d7d7d7;
  background: #ffffff;
  border-radius: 999px;
  padding: 10px 22px;
  font-family: inherit;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.add-new-btn:hover {
  background: #111111;
  color: #ffffff;
  border-color: #111111;
}

.empty-platform-message {
  padding: 40px;
  text-align: center;
  color: #8e8e93;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
}

/* モーダル */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  box-sizing: border-box;
}

.modal-content {
  width: 100%;
  max-width: 760px;
  max-height: 85vh;
  overflow-y: auto;
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  padding: 24px;
  box-sizing: border-box;
}

.youtube-modal-content {
  max-width: 760px;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
}

.btn-close {
  width: 34px;
  height: 34px;
  border: none;
  background: #f3f3f3;
  border-radius: 50%;
  font-size: 22px;
  line-height: 1;
  cursor: pointer;
}

.modal-toolbar {
  display: flex;
  gap: 8px;
  margin-bottom: 14px;
}

.modal-toolbar input {
  flex: 1;
  min-width: 0;
  padding: 10px 12px;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 10px;
  outline: none;
  font-family: inherit;
}

.modal-toolbar button {
  border: 1px solid rgba(0, 0, 0, 0.12);
  background: #ffffff;
  border-radius: 10px;
  padding: 8px 14px;
  cursor: pointer;
  font-family: inherit;
}

.modal-selected-header,
.modal-candidates-header {
  font-size: 12px;
  font-weight: 700;
  color: #666666;
  margin-bottom: 8px;
}

.modal-selected-body {
  min-height: 48px;
  padding: 10px;
  background: #f7f7f8;
  border-radius: 10px;
  margin-bottom: 18px;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
}

.selected-chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: rgba(0, 100, 220, 0.08);
  color: rgb(0, 100, 220);
  border-radius: 999px;
  padding: 4px 8px;
  font-size: 12px;
}

.selected-chip button {
  border: 0;
  background: transparent;
  color: inherit;
  cursor: pointer;
  padding: 0;
  font-size: 14px;
}

.empty-selected {
  color: #8e8e93;
  font-size: 12px;
}

.pref-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 2px;
  margin-bottom: 20px;
}

.pref-opt {
  display: flex;
  align-items: center;
  gap: 6px;
  min-height: 40px;
  padding: 6px 8px;
  border: 1px solid #eeeeee;
  background: #ffffff;
  cursor: pointer;
  font-size: 12px;
}

.pref-opt:hover {
  background: #f7f7f7;
}

.pref-opt input {
  margin: 0;
}

.modal-candidates-body {
  display: flex;
  flex-direction: column;
  gap: 2px;
  max-height: 340px;
  overflow-y: auto;
  margin-bottom: 20px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.modal-footer button {
  border: 1px solid rgba(0, 0, 0, 0.12);
  background: #ffffff;
  border-radius: 10px;
  padding: 10px 18px;
  cursor: pointer;
  font-family: inherit;
}

.modal-confirm-btn {
  background: rgb(0, 100, 220) !important;
  color: #ffffff !important;
  border-color: rgb(0, 100, 220) !important;
  font-weight: 700;
}

/* =========================
   STEP 2 / 共通ボタン
========================= */

.animated-btn {
  background-color: #111111;
  color: #ffffff;
  border: 1px solid #111111;
  padding: 16px 56px;
  font-size: 0.95rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  border-radius: 6px;
  cursor: pointer;
  opacity: 0;
  transform: translateY(12px);
  pointer-events: none;
  transition:
    opacity 0.4s ease,
    transform 0.4s ease,
    background-color 0.2s ease;
}

.animated-btn.visible {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.btn-arrow {
  display: inline-block;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.animated-btn:hover:not(:disabled) .btn-arrow {
  transform: translateX(6px);
}

.animated-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.step2-actions {
  display: flex;
  justify-content: center;
  gap: 16px;
}

.back-btn {
  background-color: transparent;
  color: #111111;
  border: 1px solid #111111;
  padding: 16px 32px;
  font-size: 0.85rem;
  border-radius: 6px;
  cursor: pointer;
}

/* =========================
   アニメーション
========================= */

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.35s ease;
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

/* =========================
   レスポンシブ
========================= */

@media (max-width: 768px) {
  .header {
    padding: 0 1rem;
  }

  .company-name {
    font-size: 0.85rem;
  }

  .app-title {
    display: none;
  }

  .step {
    padding: 0 12px;
    font-size: 0.68rem;
  }

  .content-wrapper {
    padding: 32px 16px 48px;
  }

  .form-body-vertical {
    gap: 24px;
    margin-top: 24px;
  }

  .title-area h2,
  .platform-header h2 {
    font-size: 1.75rem;
  }

  .animated-btn {
    width: 100%;
    padding: 16px 0;
  }

  .step2-actions {
    flex-direction: column-reverse;
  }

  .back-btn {
    width: 100%;
  }

  .platform-grid-container {
    overflow-x: auto;
  }

  .platform-label-column {
    min-width: 100px;
  }

  .platform-columns-wrapper {
    min-width: 720px;
  }

  .modal-overlay {
    padding: 12px;
  }

  .modal-content {
    max-height: 90vh;
    padding: 18px;
  }

  .pref-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

/* =========================
   YouTubeフォーム
========================= */

.youtube-form-container {
  width: 100%;
}

.dark-card-wrapper {
  overflow: hidden;
}

.table-scroll-container {
  width: 100%;
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
}

.pattern-matrix-table {
  width: 3393px !important;
  min-width: 3393px !important;
  table-layout: fixed;
}

.pattern-matrix-table th {
  white-space: nowrap;
}

.pattern-matrix-table td {
  position: relative;
  vertical-align: middle;
}

.matrix-input,
.matrix-select,
.pref-box,
.target-trigger {
  width: 100%;
  min-height: 36px;
  box-sizing: border-box;
}

.matrix-input,
.matrix-select {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 10px;
  padding: 8px 10px;
  font-size: 12px;
  font-family: inherit;
  outline: none;
}

.matrix-select {
  appearance: auto;
}

.matrix-input:disabled {
  background: #f2f2f7;
  color: rgba(0, 0, 0, 0.3);
}

.action-cell {
  padding: 0 !important;
}

.action-col-inner {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
}

.del-half-btn,
.copy-half-btn {
  flex: 1;
  border: 0;
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.del-half-btn {
  color: #c94b4b;
  border-bottom: 1px solid rgba(0, 0, 0, 0.07);
}

.del-half-btn:disabled {
  opacity: 0.25;
  cursor: not-allowed;
}

.del-half-btn svg,
.copy-half-btn svg {
  width: 22px;
  height: 22px;
}

.pref-box {
  text-align: left;
  cursor: pointer;
}

.pref-lines {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.pref-line {
  display: block;
  font-size: 12px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.pref-placeholder {
  color: rgba(60, 60, 67, 0.35);
}

.period-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.period-number {
  width: 58px !important;
  flex: 0 0 58px;
  text-align: center;
}

.period-unit {
  width: 72px !important;
  flex: 0 0 72px;
}

.age-wrap {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  width: 100%;
}

.age-num {
  width: 62px !important;
  text-align: center;
}

.target-trigger {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 10px;
  padding: 8px 10px;
  text-align: left;
  cursor: pointer;
  color: #8e8e93;
}

.target-trigger .selected-display {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.target-trigger .selected-display span {
  color: rgb(0, 100, 220);
  font-size: 10px;
  line-height: 1.25;
}

.notes-input {
  resize: vertical;
  min-height: 54px;
}

.empty-platform-message {
  padding: 40px;
  text-align: center;
  color: #8e8e93;
}

/* =========================
   YouTube モーダル
========================= */

.youtube-modal-content {
  max-width: 760px;
}

.modal-toolbar {
  display: flex;
  gap: 8px;
  margin-bottom: 14px;
}

.modal-toolbar input {
  flex: 1;
  min-width: 0;
  padding: 10px 12px;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 10px;
  outline: none;
}

.modal-toolbar button {
  border: 1px solid rgba(0, 0, 0, 0.12);
  background: #ffffff;
  border-radius: 10px;
  padding: 8px 14px;
  cursor: pointer;
}

.pref-grid {
  display: grid !important;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 2px;
}

.selected-chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: rgba(0, 100, 220, 0.08);
  color: rgb(0, 100, 220);
  border-radius: 999px;
  padding: 4px 8px;
  font-size: 12px;
}

.selected-chip button {
  border: 0;
  background: transparent;
  color: inherit;
  cursor: pointer;
  padding: 0;
}

.empty-selected {
  color: #8e8e93;
  font-size: 12px;
}

.modal-confirm-btn {
  background: rgb(0, 100, 220) !important;
  color: #ffffff !important;
  border-color: rgb(0, 100, 220) !important;
  font-weight: 700;
}
</style>
