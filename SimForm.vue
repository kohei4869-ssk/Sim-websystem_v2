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
            <span class="sub-label">選択したPlatformごとに詳細パラメータを設定してください</span>
          </div>

          <div class="form-platform-tabs" role="tablist" aria-label="Platform tabs">
            <button v-for="platform in selectedPlatformList" :key="platform.id" type="button" class="form-platform-tab" :class="{ active: activePlatform === platform.id }" role="tab" :aria-selected="activePlatform === platform.id" @click="selectActivePlatform(platform.id)">
              <span class="form-platform-tab-logo">{{ platform.name === 'YOUTUBE' ? '▶' : platform.name === 'META' ? 'f' : 'Y!' }}</span>
              <span>{{ platform.name }}</span>
            </button>
          </div>

          <div v-if="activePlatform === 'youtube'" class="platform-table-panel">
            <div v-if="platforms.youtube.selected" class="youtube-form-container">
              <div class="dark-card-wrapper">
                <div class="table-scroll-container">
                  <table class="pattern-matrix-table">
                    <colgroup>
                      <col style="width:72px"><col style="width:32px"><col style="width:150px"><col style="width:240px"><col style="width:190px"><col style="width:190px"><col style="width:160px"><col style="width:140px"><col style="width:160px"><col style="width:180px"><col style="width:90px"><col style="width:110px"><col style="width:110px"><col style="width:110px"><col style="width:240px"><col style="width:240px"><col style="width:240px"><col style="width:120px"><col style="width:450px">
                    </colgroup>
                    <thead><tr><th>操作</th><th>#</th><th>メニュー</th><th>配信面</th><th>縦型</th><th>横型</th><th>配信期間</th><th>予算</th><th>都道府県</th><th>市町村</th><th>性別</th><th>年齢①</th><th>年齢②</th><th>年齢③</th><th>ターゲット①</th><th>ターゲット②</th><th>ターゲット③</th><th>デバイス</th><th>備考</th></tr></thead>
                    <tbody>
                      <tr v-for="(pattern,index) in youtubePatterns" :key="pattern.id">
                        <td class="action-cell"><div class="action-col-inner"><button type="button" class="del-half-btn" :disabled="youtubePatterns.length <= 1" @click="removeYoutubePattern(index)" aria-label="削除"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></button><button type="button" class="copy-half-btn" @click="copyYoutubePattern(index)" aria-label="複製"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M6 15H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1"/></svg></button></div></td>
                        <td class="text-center font-bold">{{ index + 1 }}</td>
                        <td><select v-model="pattern.menu" class="matrix-select"><option value="">選択</option><option v-for="v in youtubeMenus" :key="v" :value="v">{{ v }}</option></select></td>
                        <td><select v-model="pattern.placement" class="matrix-select"><option value="">選択</option><option v-for="v in youtubePlacements" :key="v" :value="v">{{ v }}</option></select></td>
                        <td><select v-model="pattern.verticalCreative" class="matrix-select"><option value="">選択</option><option v-for="v in verticalCreativeOptions" :key="v" :value="v">{{ v }}</option></select></td>
                        <td><select v-model="pattern.horizontalCreative" class="matrix-select"><option value="">選択</option><option v-for="v in horizontalCreativeOptions" :key="v" :value="v">{{ v }}</option></select></td>
                        <td><div class="period-wrap"><input v-model.number="pattern.periodNumber" type="number" min="1" class="matrix-input period-number" placeholder="数値"><select v-model="pattern.periodUnit" class="matrix-select period-unit"><option value="日">日</option><option value="週">週</option><option value="月">月</option></select></div></td>
                        <td><input v-model.number="pattern.budget" type="number" min="0" class="matrix-input text-right" placeholder="1000000"></td>
                        <td><button type="button" class="pref-box" @click="openPrefModal(index)"><span v-if="pattern.prefNames.length" class="pref-lines"><span v-for="name in pattern.prefNames" :key="name" class="pref-line">{{ name }}</span></span><span v-else class="pref-placeholder">エリア選択</span></button></td>
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
                <div class="add-pattern-area"><button type="button" class="add-new-btn" @click="addYoutubePattern">＋ New</button></div>
              </div>
            </div>
          </div>

          <div v-else-if="activePlatform === 'meta'" class="platform-table-panel"><div class="pattern-list-card"><div class="pattern-list-header"><div><strong>META PATTERNS</strong><span>0 pattern</span></div><p>Metaのパターン入力テーブル</p></div><div class="pattern-table-wrap"><table class="pattern-list-table platform-placeholder-table"><thead><tr><th>#</th><th>メニュー</th><th>配信面</th><th>クリエイティブ</th><th>配信期間</th><th>予算</th><th>エリア</th><th>性別</th><th>デバイス</th><th>備考</th></tr></thead><tbody><tr><td colspan="10" class="empty-table-cell">Metaフォームをここに実装します</td></tr></tbody></table></div><div class="add-pattern-area"><button type="button" class="add-new-btn">＋ New Pattern</button></div></div></div>

          <div v-else-if="activePlatform === 'listing'" class="platform-table-panel"><div class="pattern-list-card"><div class="pattern-list-header"><div><strong>LISTING PATTERNS</strong><span>0 pattern</span></div><p>Listingのパターン入力テーブル</p></div><div class="pattern-table-wrap"><table class="pattern-list-table platform-placeholder-table"><thead><tr><th>#</th><th>メニュー</th><th>配信面</th><th>クリエイティブ</th><th>配信期間</th><th>予算</th><th>エリア</th><th>性別</th><th>デバイス</th><th>備考</th></tr></thead><tbody><tr><td colspan="10" class="empty-table-cell">Listingフォームをここに実装します</td></tr></tbody></table></div><div class="add-pattern-area"><button type="button" class="add-new-btn">＋ New Pattern</button></div></div></div>

          <div v-else class="empty-platform-message">PLATFORMSでPlatformを選択してください。</div>

          <div class="action-area step2-actions"><button class="back-btn" @click="currentStep = 2">&larr; PLATFORMSに戻る</button><button class="next-btn animated-btn visible" @click="submitForm"><span>送信する</span><span class="btn-arrow">&rarr;</span></button></div>
        </section>
      </transition>

      <!-- 都道府県モーダル -->
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closePrefModal"><div class="modal-content youtube-modal-content"><div class="modal-header"><h3>都道府県を選択</h3><button type="button" class="btn-close" @click="closePrefModal">×</button></div><div class="modal-toolbar"><input v-model="prefSearch" type="search" placeholder="都道府県を検索"><button type="button" @click="clearPrefSelection">クリア</button></div><div class="modal-selected-header">選択済み</div><div class="modal-selected-body"><span v-for="name in selectedPrefNames" :key="name" class="selected-chip">{{ name }} <button type="button" @click="removePref(name)">×</button></span><span v-if="!selectedPrefNames.length" class="empty-selected">未選択</span></div><div class="modal-candidates-header">候補</div><div class="modal-candidates-body pref-grid"><label v-for="pref in filteredPrefList" :key="pref.id" class="pref-opt"><input type="checkbox" :value="pref.name" v-model="pendingPrefNames"><span>{{ pref.name }}</span></label></div><div class="modal-footer"><button type="button" @click="closePrefModal">キャンセル</button><button type="button" class="modal-confirm-btn" @click="confirmPrefSelection">確定</button></div></div></div>

      <!-- ターゲットモーダル -->
      <div v-if="targetModalOpen" class="modal-overlay" @click.self="closeTargetModal"><div class="modal-content youtube-modal-content"><div class="modal-header"><h3>{{ targetModalTitle }}</h3><button type="button" class="btn-close" @click="closeTargetModal">×</button></div><div class="modal-toolbar"><input v-model="targetSearch" type="search" placeholder="候補を検索"><button type="button" @click="clearTargetSelection">クリア</button></div><div class="modal-selected-header">選択済み</div><div class="modal-selected-body"><span v-for="name in pendingTargetNames" :key="name" class="selected-chip">{{ name }} <button type="button" @click="removeTarget(name)">×</button></span><span v-if="!pendingTargetNames.length" class="empty-selected">未選択</span></div><div class="modal-candidates-header">候補</div><div class="modal-candidates-body"><label v-for="name in filteredTargetOptions" :key="name" class="pref-opt"><input type="checkbox" :value="name" v-model="pendingTargetNames"><span>{{ name }}</span></label></div><div class="modal-footer"><button type="button" @click="closeTargetModal">キャンセル</button><button type="button" class="modal-confirm-btn" @click="confirmTargetSelection">確定</button></div></div></div>
    </main>
  </div>
</template>

<script setup>
import { reactive, computed, ref, onMounted, watch } from 'vue'
import { DotLottieVue } from '@lottiefiles/dotlottie-vue'

const currentStep = ref(1)
const activeFieldIndex = ref(1)
const hoveredPlatform = ref(null)
const activePlatform = ref(null)
const applicantNameInput = ref(null)
const lottieRefs = reactive({})

onMounted(() => { if (applicantNameInput.value) applicantNameInput.value.focus() })

const formData = reactive({ applicantName: '', department: '', email: '', clientName: '', projectName: '', dueDate: '', remarks: '' })

let youtubePatternSeq = 1
const createEmptyPattern = () => ({ id: `yt-${Date.now()}-${youtubePatternSeq++}`, menu: '', placement: '', verticalCreative: '', horizontalCreative: '', periodNumber: null, periodUnit: '日', budget: null, prefId: '', prefName: '', prefNames: [], city: '', gender: 'all', age1: '', age2: '', age3: '', target1: [], target2: [], target3: [], device: 'all', notes: '' })
const youtubePatterns = reactive([createEmptyPattern()])
const youtubeMenus = ['TrueView インストリーム', 'YouTube Shorts', 'Video Action Campaign', 'YouTube Select']
const youtubePlacements = ['YouTube 動画', 'YouTube Shorts', 'YouTube ホーム', 'YouTube 検索']
const verticalCreativeOptions = ['6秒', '15秒', '30秒', '60秒', '90秒以上']
const horizontalCreativeOptions = ['6秒', '15秒', '30秒', '60秒', '90秒以上']

const prefList = [
  { id:'national', name:'全国' }, { id:'hokkaido', name:'北海道' }, { id:'aomori', name:'青森県' }, { id:'iwate', name:'岩手県' }, { id:'miyagi', name:'宮城県' }, { id:'akita', name:'秋田県' }, { id:'yamagata', name:'山形県' }, { id:'fukushima', name:'福島県' },
  { id:'ibaraki', name:'茨城県' }, { id:'tochigi', name:'栃木県' }, { id:'gunma', name:'群馬県' }, { id:'saitama', name:'埼玉県' }, { id:'chiba', name:'千葉県' }, { id:'tokyo', name:'東京都' }, { id:'kanagawa', name:'神奈川県' },
  { id:'niigata', name:'新潟県' }, { id:'toyama', name:'富山県' }, { id:'ishikawa', name:'石川県' }, { id:'fukui', name:'福井県' }, { id:'yamanashi', name:'山梨県' }, { id:'nagano', name:'長野県' }, { id:'gifu', name:'岐阜県' }, { id:'shizuoka', name:'静岡県' }, { id:'aichi', name:'愛知県' },
  { id:'mie', name:'三重県' }, { id:'shiga', name:'滋賀県' }, { id:'kyoto', name:'京都府' }, { id:'osaka', name:'大阪府' }, { id:'hyogo', name:'兵庫県' }, { id:'nara', name:'奈良県' }, { id:'wakayama', name:'和歌山県' },
  { id:'tottori', name:'鳥取県' }, { id:'shimane', name:'島根県' }, { id:'okayama', name:'岡山県' }, { id:'hiroshima', name:'広島県' }, { id:'yamaguchi', name:'山口県' }, { id:'tokushima', name:'徳島県' }, { id:'kagawa', name:'香川県' }, { id:'ehime', name:'愛媛県' }, { id:'kochi', name:'高知県' },
  { id:'fukuoka', name:'福岡県' }, { id:'saga', name:'佐賀県' }, { id:'nagasaki', name:'長崎県' }, { id:'kumamoto', name:'熊本県' }, { id:'oita', name:'大分県' }, { id:'miyazaki', name:'宮崎県' }, { id:'kagoshima', name:'鹿児島県' }, { id:'okinawa', name:'沖縄県' }
]

const isModalOpen = ref(false)
const selectedRowIndex = ref(null)
const prefSearch = ref('')
const pendingPrefNames = ref([])
const filteredPrefList = computed(() => { const q = prefSearch.value.trim().toLowerCase(); return q ? prefList.filter(p => p.name.toLowerCase().includes(q)) : prefList })
const selectedPrefNames = computed(() => pendingPrefNames.value)
const openPrefModal = rowIndex => { selectedRowIndex.value = rowIndex; pendingPrefNames.value = [...youtubePatterns[rowIndex].prefNames]; prefSearch.value = ''; isModalOpen.value = true }
const closePrefModal = () => { isModalOpen.value = false; selectedRowIndex.value = null; prefSearch.value = '' }
const clearPrefSelection = () => { pendingPrefNames.value = [] }
const removePref = name => { pendingPrefNames.value = pendingPrefNames.value.filter(v => v !== name) }
const confirmPrefSelection = () => { if (selectedRowIndex.value === null) return; const row = youtubePatterns[selectedRowIndex.value]; row.prefNames = [...pendingPrefNames.value]; row.prefId = row.prefNames[0] || ''; row.prefName = row.prefNames.join(' / '); if (row.prefNames.includes('全国')) row.city = ''; closePrefModal() }

const targetLists = { 1: ['アフィニティカテゴリ','スポーツファン','旅行好き','テクノロジー愛好者','美容・ファッション','グルメ・料理','自動車愛好者','ゲーム愛好者','音楽ファン','ニュース好き','アウトドア好き'], 2: ['購買意向の強いオーディエンス','家電・電子機器','自動車','旅行','金融サービス','不動産','求人・転職','教育','美容商品','通信サービス','ソフトウェア'], 3: ['詳細なユーザー属性','子どもあり','大学生','会社員','経営者','住宅所有者','最近結婚した','最近引っ越した','親','世帯収入上位層'] }
const targetModalOpen = ref(false)
const targetRowIndex = ref(null)
const targetModalColumn = ref(1)
const targetSearch = ref('')
const pendingTargetNames = ref([])
const targetModalTitle = computed(() => `ターゲット${['①','②','③'][targetModalColumn.value - 1]}を選択`)
const filteredTargetOptions = computed(() => { const q = targetSearch.value.trim().toLowerCase(); const list = targetLists[targetModalColumn.value] || []; return q ? list.filter(v => v.toLowerCase().includes(q)) : list })
const openTargetModal = (rowIndex, column) => { targetRowIndex.value = rowIndex; targetModalColumn.value = column; pendingTargetNames.value = [...youtubePatterns[rowIndex][`target${column}`]]; targetSearch.value = ''; targetModalOpen.value = true }
const closeTargetModal = () => { targetModalOpen.value = false; targetRowIndex.value = null; targetSearch.value = '' }
const clearTargetSelection = () => { pendingTargetNames.value = [] }
const removeTarget = name => { pendingTargetNames.value = pendingTargetNames.value.filter(v => v !== name) }
const confirmTargetSelection = () => { if (targetRowIndex.value === null) return; youtubePatterns[targetRowIndex.value][`target${targetModalColumn.value}`] = [...pendingTargetNames.value]; closeTargetModal() }

const addYoutubePattern = () => { youtubePatterns.push(createEmptyPattern()) }
const removeYoutubePattern = index => { if (youtubePatterns.length > 1) youtubePatterns.splice(index, 1) }
const copyYoutubePattern = index => { const copy = JSON.parse(JSON.stringify(youtubePatterns[index])); copy.id = `yt-${Date.now()}-${youtubePatternSeq++}`; youtubePatterns.splice(index + 1, 0, copy) }

const touched = reactive({ email: false })
const departmentOptions = [{ value: 'dept1', label: '第1営業部' }, { value: 'dept2', label: '第2営業部' }, { value: 'dev', label: '開発部' }]
const isFieldDimmed = (index, value) => { const isEmpty = !value || (typeof value === 'string' && value.trim() === ''); return activeFieldIndex.value !== index && isEmpty }
const isEmailInvalid = computed(() => { if (!formData.email.trim()) return false; const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; return touched.email && !emailPattern.test(formData.email.trim()) })
const isStep1Valid = computed(() => formData.applicantName.trim() !== '' && formData.clientName.trim() !== '' && formData.projectName.trim() !== '' && !isEmailInvalid.value)

const platformList = [
  { id: 'meta', name: 'META', logos: [{ lottiePath: '/images_json/Meta.lottie', class: 'meta' }] },
  { id: 'listing', name: 'LISTING', logos: [{ lottiePath: '/images_json/Listing.lottie', class: 'listing' }] },
  { id: 'youtube', name: 'YOUTUBE', logos: [{ lottiePath: '/images_json/YouTube.lottie', class: 'youtube' }] }
]
const platforms = reactive(platformList.reduce((acc, p) => { acc[p.id] = { selected: false, marginType: 'percent', marginValue: 20 }; return acc }, {}))
const selectedPlatformList = computed(() => platformList.filter(p => platforms[p.id]?.selected))
const selectActivePlatform = id => { if (platforms[id]?.selected) activePlatform.value = id }
const syncActivePlatform = () => { const selected = selectedPlatformList.value; if (!selected.length) { activePlatform.value = null; return }; if (!activePlatform.value || !platforms[activePlatform.value]?.selected) activePlatform.value = selected[0].id }
watch(() => selectedPlatformList.value.map(p => p.id).join('|'), syncActivePlatform)

const setLottieRef = (el, id) => { if (el) lottieRefs[id] = el }
const getDotLottie = id => { const lottieComp = lottieRefs[id]; if (!lottieComp) return null; return lottieComp.getDotLottieInstance ? lottieComp.getDotLottieInstance() : null }
const onMouseEnter = id => { hoveredPlatform.value = id; const dotLottie = getDotLottie(id); if (dotLottie) dotLottie.play() }
const onMouseLeave = id => { hoveredPlatform.value = null; if (!platforms[id].selected) { const dotLottie = getDotLottie(id); if (dotLottie) dotLottie.stop() } }
const togglePlatform = id => { platforms[id].selected = !platforms[id].selected; const dotLottie = getDotLottie(id); if (dotLottie) { if (platforms[id].selected) dotLottie.play(); else if (hoveredPlatform.value !== id) dotLottie.stop() }; syncActivePlatform() }
const isStep2Valid = computed(() => Object.values(platforms).some(p => p.selected))
const submitForm = () => { const payload = { entry: formData, platforms, youtube: { patterns: youtubePatterns } }; console.log('送信データ:', payload); alert('送信が完了しました！') }
</script>

<style scoped>
/* Platform tabs above each form table */
.form-platform-tabs{display:flex;align-items:stretch;gap:8px;margin:0 0 14px;padding:4px;border-bottom:1px solid rgba(0,0,0,.08);overflow-x:auto}
.form-platform-tab{display:flex;align-items:center;justify-content:center;gap:8px;min-width:130px;padding:10px 18px;border:1px solid rgba(0,0,0,.08);border-bottom:3px solid transparent;border-radius:10px 10px 0 0;background:#fff;color:rgba(0,0,0,.52);font-weight:700;letter-spacing:1px;cursor:pointer;transition:.18s ease}
.form-platform-tab:hover{background:rgba(0,122,255,.04)}
.form-platform-tab.active{color:rgb(0,122,255);border-color:rgba(0,122,255,.25);border-bottom-color:rgb(0,122,255);background:rgba(0,122,255,.08)}
.form-platform-tab-logo{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:7px;background:rgba(0,0,0,.06);font-size:12px;font-weight:800}
.form-platform-tab.active .form-platform-tab-logo{background:rgba(0,122,255,.15)}
.platform-table-panel{width:100%}
.platform-placeholder-table{min-width:1000px}
.empty-table-cell{padding:40px 16px!important;text-align:center;color:rgba(0,0,0,.42)}
</style>
