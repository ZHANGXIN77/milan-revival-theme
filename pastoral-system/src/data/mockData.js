// Mock data for Youth Pastoral System
export const STAGES = [
  { id: 0, name: '慕道友', color: 'var(--stage-0)', bgColor: 'rgba(107,127,163,0.15)', tasks: ['稳定参加主日崇拜', '参加福音小组', '与小组长建立信任关系'] },
  { id: 1, name: '初信', color: 'var(--stage-1)', bgColor: 'rgba(90,138,110,0.15)', tasks: ['完成初信造就课程', '受洗', '加入小组', '开始个人灵修'] },
  { id: 2, name: '委身', color: 'var(--stage-2)', bgColor: 'rgba(201,168,76,0.15)', tasks: ['稳定服事', '参加门训课程', '团契参与教会活动'] },
  { id: 3, name: '门徒', color: 'var(--stage-3)', bgColor: 'rgba(155,109,181,0.15)', tasks: ['带领小组', '一对一陪伴他人', '参加领袖培训'] },
  { id: 4, name: '领袖', color: 'var(--stage-4)', bgColor: 'rgba(192,97,78,0.15)', tasks: ['带领事工', '接受领袖培训', '能够牧养他人'] },
];

export const GROUPS = [
  { id: 1, name: 'A组（晨星）', leaderId: 3, description: '大学生小组' },
  { id: 2, name: 'B组（烛光）', leaderId: 6, description: '高中生小组' },
  { id: 3, name: 'C组（橄榄）', leaderId: 9, description: '混合年龄小组' },
  { id: 4, name: 'D组（磐石）', leaderId: 12, description: '大学生小组' },
  { id: 5, name: 'E组（葡萄藤）', leaderId: 15, description: '高中生小组' },
];

export const MEMBERS = [
  { id: 0, name: '何恩典', englishName: 'Grace', gender: '男', dob: '1980-06-15', school: '米兰复兴教会 牧区长', phone: '+39 333 0000001', email: 'pastor@milanorevival.com', parentContact: '', joinDate: '2020-01-01', baptized: true, baptizeDate: '2000-04-23', mbti: null, groupId: null, stage: 4, status: '活跃', avatar: null, gdprConsent: true },
  { id: 1, name: '张明远', englishName: 'Marco', gender: '男', dob: '2003-05-12', school: '米兰理工大学 大二', phone: '+39 333 1234567', email: 'mingyuan@gmail.com', parentContact: '张父 +39 333 9876543', joinDate: '2023-09-01', baptized: true, baptizeDate: '2024-03-17', mbti: 'ENFJ', groupId: 1, stage: 3, status: '活跃', avatar: null, gdprConsent: true },
  { id: 2, name: '李晓雨', englishName: 'Laura', gender: '女', dob: '2004-11-23', school: '米兰大学 大一', phone: '+39 333 2345678', email: 'xiaoyu@gmail.com', parentContact: '李母 +39 333 8765432', joinDate: '2024-02-15', baptized: false, baptizeDate: null, mbti: 'INFP', groupId: 1, stage: 1, status: '活跃', avatar: null, gdprConsent: true },
  { id: 3, name: '王建国', englishName: 'Kevin', gender: '男', dob: '2001-07-08', school: '博科尼大学 大四', phone: '+39 333 3456789', email: 'jianguo@gmail.com', parentContact: '王父 +39 333 7654321', joinDate: '2022-03-10', baptized: true, baptizeDate: '2022-12-25', mbti: 'ESTJ', groupId: 1, stage: 4, status: '活跃', avatar: null, gdprConsent: true, isGroupLeader: true },
  { id: 4, name: '陈思琪', englishName: 'Sophie', gender: '女', dob: '2005-03-30', school: '米兰国际学校 高三', phone: '+39 333 4567890', email: 'siqi@gmail.com', parentContact: '陈母 +39 333 6543210', joinDate: '2024-09-05', baptized: false, baptizeDate: null, mbti: null, groupId: 2, stage: 0, status: '活跃', avatar: null, gdprConsent: true },
  { id: 5, name: '刘浩宇', englishName: 'Hugo', gender: '男', dob: '2004-08-17', school: '米兰理工大学 大一', phone: '+39 333 5678901', email: 'haoyu@gmail.com', parentContact: '刘父 +39 333 5432109', joinDate: '2023-11-20', baptized: true, baptizeDate: '2024-06-09', mbti: 'INTP', groupId: 2, stage: 2, status: '活跃', avatar: null, gdprConsent: true },
  { id: 6, name: '孙雅婷', englishName: 'Valentina', gender: '女', dob: '2006-01-14', school: '米兰国际学校 高二', phone: '+39 333 6789012', email: 'yating@gmail.com', parentContact: '孙母 +39 333 4321098', joinDate: '2023-05-08', baptized: false, baptizeDate: null, mbti: 'ESFJ', groupId: 2, stage: 2, status: '活跃', avatar: null, gdprConsent: true, isGroupLeader: true },
  { id: 7, name: '赵子涵', englishName: 'Daniel', gender: '男', dob: '2003-09-26', school: '米兰大学 大二', phone: '+39 333 7890123', email: 'zihan@gmail.com', parentContact: '赵父 +39 333 3210987', joinDate: '2022-09-15', baptized: true, baptizeDate: '2023-04-09', mbti: 'INTJ', groupId: 3, stage: 3, status: '活跃', avatar: null, gdprConsent: true },
  { id: 8, name: '黄美玲', englishName: 'Melinda', gender: '女', dob: '2005-06-03', school: '米兰国际学校 高一', phone: '+39 333 8901234', email: 'meiling@gmail.com', parentContact: '黄母 +39 333 2109876', joinDate: '2024-04-20', baptized: false, baptizeDate: null, mbti: 'ISFJ', groupId: 3, stage: 0, status: '活跃', avatar: null, gdprConsent: true },
  { id: 9, name: '周大卫', englishName: 'David', gender: '男', dob: '2000-12-11', school: '米兰理工大学 研一', phone: '+39 333 9012345', email: 'david@gmail.com', parentContact: '周父 +39 333 1098765', joinDate: '2021-09-01', baptized: true, baptizeDate: '2022-01-30', mbti: 'ENFP', groupId: 3, stage: 4, status: '活跃', avatar: null, gdprConsent: true, isGroupLeader: true },
  { id: 10, name: '吴凤仪', englishName: 'Fiona', gender: '女', dob: '2004-04-22', school: '博科尼大学 大二', phone: '+39 333 0123456', email: 'fengyi@gmail.com', parentContact: '吴母 +39 333 0987654', joinDate: '2023-07-12', baptized: false, baptizeDate: null, mbti: 'ENFJ', groupId: 4, stage: 1, status: '活跃', avatar: null, gdprConsent: true },
  { id: 11, name: '郑浩然', englishName: 'Harvey', gender: '男', dob: '2005-10-07', school: '米兰国际学校 高二', phone: '+39 333 1234509', email: 'haoran@gmail.com', parentContact: '郑父 +39 333 9876500', joinDate: '2024-01-28', baptized: false, baptizeDate: null, mbti: null, groupId: 4, stage: 0, status: '活跃', avatar: null, gdprConsent: false },
  { id: 12, name: '林静怡', englishName: 'Jenny', gender: '女', dob: '2002-02-19', school: '米兰大学 大三', phone: '+39 333 2345001', email: 'jingyi@gmail.com', parentContact: '林母 +39 333 8765001', joinDate: '2022-06-05', baptized: true, baptizeDate: '2023-06-11', mbti: 'INFJ', groupId: 4, stage: 4, status: '活跃', avatar: null, gdprConsent: true, isGroupLeader: true },
  { id: 13, name: '徐俊豪', englishName: 'Justin', gender: '男', dob: '2003-11-30', school: '米兰理工大学 大三', phone: '+39 333 3456002', email: 'junhao@gmail.com', parentContact: '徐父 +39 333 7654002', joinDate: '2023-03-18', baptized: true, baptizeDate: '2023-12-24', mbti: 'ESTP', groupId: 5, stage: 2, status: '活跃', avatar: null, gdprConsent: true },
  { id: 14, name: '曾小芳', englishName: 'Fanny', gender: '女', dob: '2006-07-25', school: '米兰国际学校 高一', phone: '+39 333 4567003', email: 'xiaofang@gmail.com', parentContact: '曾母 +39 333 6543003', joinDate: '2025-01-10', baptized: false, baptizeDate: null, mbti: null, groupId: 5, stage: 0, status: '活跃', avatar: null, gdprConsent: true },
  { id: 15, name: '方子豪', englishName: 'Zac', gender: '男', dob: '2001-05-16', school: '博科尼大学 大四', phone: '+39 333 5678004', email: 'zihao@gmail.com', parentContact: '方父 +39 333 5432004', joinDate: '2021-11-07', baptized: true, baptizeDate: '2022-04-17', mbti: 'ENTJ', groupId: 5, stage: 4, status: '活跃', avatar: null, gdprConsent: true, isGroupLeader: true },
];

export const ATTENDANCE = [
  { id: 1, date: '2026-03-29', records: [
    { memberId: 1, present: true }, { memberId: 2, present: true }, { memberId: 3, present: true },
    { memberId: 4, present: false }, { memberId: 5, present: true }, { memberId: 6, present: true },
    { memberId: 7, present: true }, { memberId: 8, present: false }, { memberId: 9, present: true },
    { memberId: 10, present: true }, { memberId: 11, present: false }, { memberId: 12, present: true },
    { memberId: 13, present: true }, { memberId: 14, present: true }, { memberId: 15, present: true },
  ]},
  { id: 2, date: '2026-03-22', records: [
    { memberId: 1, present: true }, { memberId: 2, present: false }, { memberId: 3, present: true },
    { memberId: 4, present: true }, { memberId: 5, present: true }, { memberId: 6, present: false },
    { memberId: 7, present: true }, { memberId: 8, present: true }, { memberId: 9, present: true },
    { memberId: 10, present: false }, { memberId: 11, present: true }, { memberId: 12, present: true },
    { memberId: 13, present: false }, { memberId: 14, present: true }, { memberId: 15, present: true },
  ]},
  { id: 3, date: '2026-03-15', records: [
    { memberId: 1, present: true }, { memberId: 2, present: true }, { memberId: 3, present: false },
    { memberId: 4, present: false }, { memberId: 5, present: true }, { memberId: 6, present: true },
    { memberId: 7, present: false }, { memberId: 8, present: false }, { memberId: 9, present: true },
    { memberId: 10, present: true }, { memberId: 11, present: false }, { memberId: 12, present: true },
    { memberId: 13, present: true }, { memberId: 14, present: false }, { memberId: 15, present: true },
  ]},
];

export const NOTES = [
  { id: 1, memberId: 2, authorId: 3, type: '关怀谈话', content: '李晓雨今天分享了她最近的信仰困惑，主要关于如何在学业压力中保持灵修生活。我们一起祷告，并约定下周再跟进。她的心是敞开的，感觉这段时间信仰在成长。', date: '2026-03-28T14:30:00' },
  { id: 2, memberId: 4, authorId: 6, type: '关怀谈话', content: '陈思琪第一次参加小组。比较安静，但很专注。课后单独和她聊了一会，她说是朋友带来的。对教会和信仰都还很陌生，需要耐心关怀。', date: '2026-03-27T17:00:00' },
  { id: 3, memberId: 8, authorId: 9, type: '异常状况', content: '黄美玲连续两周缺席。联系了她，说是功课很重，压力大。需要持续关注，计划本周去探访她。', date: '2026-03-26T10:00:00' },
  { id: 4, memberId: 1, authorId: 3, type: '代祷事项', content: '张明远的父母关系出现了问题，他很困扰。请大家为他家庭关系的和睦代祷，也为他在此困境中能经历神的同在。', date: '2026-03-25T16:00:00' },
  { id: 5, memberId: 5, authorId: 6, type: '关怀谈话', content: '刘浩宇完成了初信造就课程，决志受洗。我们商量了受洗的时间，计划复活节崇拜时受洗，为他感恩！', date: '2026-03-24T15:30:00' },
  { id: 6, memberId: 11, authorId: 12, type: '异常状况', content: '郑浩然的GDPR同意书尚未取得，需要联系其家长。同时发现他最近状态不太好，可能有一些个人问题。', date: '2026-03-23T09:00:00' },
  { id: 7, memberId: 7, authorId: 9, type: '关怀谈话', content: '赵子涵正在积极带领小组讨论，表现出很好的领袖气质。建议牧区长考虑给予更多领袖培训的机会。', date: '2026-03-22T18:00:00' },
];

export const PRAYER_REQUESTS = [
  { id: 1, title: '为张明远的家庭代祷', content: '张明远的父母关系出现问题，请为他家庭关系的和睦代祷，也为他在困境中经历神的同在和平安。', authorId: 3, date: '2026-03-25', status: '进行中', visibility: 'all' },
  { id: 2, title: '黄美玲的学业压力', content: '黄美玲最近学业压力很大，已连续缺席两周，请为她代祷，求神赐她智慧和力量面对功课，也保守她不要离开团契。', authorId: 9, date: '2026-03-26', status: '进行中', visibility: 'leaders' },
  { id: 3, title: '刘浩宇受洗感恩', content: '感谢神！刘浩宇决志受洗，计划复活节受洗，求神坚固他的信心，在新的属灵生命旅途上不断成长。', authorId: 6, date: '2026-03-24', status: '进行中', visibility: 'all' },
  { id: 4, title: '郑浩然家庭沟通', content: '郑浩然家长尚未签署GDPR同意，且孩子目前状态欠佳，需要智慧地与家长沟通，请为此代祷。', authorId: 12, date: '2026-03-23', status: '进行中', visibility: 'pastor' },
  { id: 5, title: '青年营会筹备', content: '2026复活节营会在下周开始，请为整个营会的筹备、出席率、圣灵工作代祷。', authorId: 1, date: '2026-03-20', status: '进行中', visibility: 'all' },
];

export const MEETINGS = [
  // A组（晨星）
  { id: 1,  groupId: 1, date: '2026-04-05', time: '15:00', location: '王建国家', address: 'Via Torino 12, Milano', theme: '马可福音第1章研读', notes: '请提前阅读经文，会后一起吃饭' },
  { id: 2,  groupId: 1, date: '2026-04-12', time: '15:00', location: '王建国家', address: 'Via Torino 12, Milano', theme: '马可福音第2章：医治与赦罪', notes: '' },
  { id: 3,  groupId: 1, date: '2026-04-19', time: '14:30', location: '教会大厅', address: '', theme: '复活节联合庆典', notes: '全牧区共同聚会，请准时到达' },
  { id: 4,  groupId: 1, date: '2026-04-26', time: '15:00', location: '王建国家', address: 'Via Torino 12, Milano', theme: '马可福音第3章：呼召十二门徒', notes: '' },
  // B组（烛光）
  { id: 5,  groupId: 2, date: '2026-04-04', time: '16:00', location: '孙雅婷家', address: 'Via Milano 5, Milano', theme: '诗篇第23篇默想', notes: '欢迎新朋友参加' },
  { id: 6,  groupId: 2, date: '2026-04-11', time: '16:00', location: '孙雅婷家', address: 'Via Milano 5, Milano', theme: '信仰与学业——如何平衡？', notes: '' },
  { id: 7,  groupId: 2, date: '2026-04-18', time: '16:00', location: '教会大厅', address: '', theme: '复活节联合庆典', notes: '全牧区共同聚会' },
  { id: 8,  groupId: 2, date: '2026-04-25', time: '16:00', location: '孙雅婷家', address: 'Via Milano 5, Milano', theme: '约翰福音第1章：道成肉身', notes: '' },
  // C组（橄榄）
  { id: 9,  groupId: 3, date: '2026-04-03', time: '19:00', location: '教会小厅（3楼）', address: '', theme: '路加福音第15章：三个比喻', notes: '带上笔记本' },
  { id: 10, groupId: 3, date: '2026-04-10', time: '19:00', location: '教会小厅（3楼）', address: '', theme: '祷告的功课', notes: '' },
  { id: 11, groupId: 3, date: '2026-04-17', time: '19:00', location: '教会大厅', address: '', theme: '复活节联合庆典', notes: '全牧区共同聚会' },
  { id: 12, groupId: 3, date: '2026-04-24', time: '19:00', location: '教会小厅（3楼）', address: '', theme: '路加福音第16章研读', notes: '' },
  // D组（磐石）
  { id: 13, groupId: 4, date: '2026-04-05', time: '17:00', location: '林静怡家', address: 'Via Venezia 8, Milano', theme: '约拿书：逃跑与恢复', notes: '欢迎带朋友来' },
  { id: 14, groupId: 4, date: '2026-04-12', time: '17:00', location: '林静怡家', address: 'Via Venezia 8, Milano', theme: '约拿书第2-3章', notes: '' },
  { id: 15, groupId: 4, date: '2026-04-19', time: '17:00', location: '教会大厅', address: '', theme: '复活节联合庆典', notes: '全牧区共同聚会' },
  { id: 16, groupId: 4, date: '2026-04-26', time: '17:00', location: '林静怡家', address: 'Via Venezia 8, Milano', theme: '身份认同：我是谁？', notes: '' },
  // E组（葡萄藤）
  { id: 17, groupId: 5, date: '2026-04-04', time: '15:00', location: '方子豪家', address: 'Via Roma 20, Milano', theme: '约翰福音第15章：葡萄树与枝子', notes: '' },
  { id: 18, groupId: 5, date: '2026-04-11', time: '15:00', location: '方子豪家', address: 'Via Roma 20, Milano', theme: '门徒训练：服事的心态', notes: '' },
  { id: 19, groupId: 5, date: '2026-04-18', time: '15:00', location: '教会大厅', address: '', theme: '复活节联合庆典', notes: '全牧区共同聚会' },
  { id: 20, groupId: 5, date: '2026-04-25', time: '15:00', location: '方子豪家', address: 'Via Roma 20, Milano', theme: '如何传福音给朋友', notes: '实践分享环节' },
];

