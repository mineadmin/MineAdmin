// data/resourceData.ts

function generateData() {
  const storageModes = [1, 2, 3]
  const fileTypes = ['image', 'document', 'audio', 'video', 'package']
  const mimeTypes = {
    image: ['image/jpeg', 'image/png', 'image/gif'],
    document: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    audio: ['audio/mpeg', 'audio/wav'],
    video: ['video/mp4', 'video/avi'],
    package: ['application/zip', 'application/x-rar-compressed', 'application/x-gzip'],
  }
  const fileNames = [
    '季度销售业绩与市场趋势分析报告',
    '全球市场扩张战略规划文档',
    '跨部门协作流程优化实施方案',
    '高效团队管理与领导力发展指南',
    '客户关系管理系统使用手册',
    'Corporate Social Responsibility Report',
    'Competitor Analysis Report',
    'Risk Management Framework Document',
    'Corporate Culture Introduction',
    'Impact Analysis and Implementation Guide of Global Economic Situation Changes on Corporate Strategy Adjustment - August 2023 Edition 1 (23)',
    'Advanced Technology R&D Project Management and Market Promotion Plan - August 2023 Edition 2 (57)',
    'Corporate Brand Reshaping and Market Positioning Enhancement Strategy - August 2023 Edition 14 (59)',
    '年度重点项目进度跟踪与评估报告',
    '企业知识产权保护与专利申请指导',
    '国际贸易合同编写与法律风险防范',
    '电子商务平台运营管理手册',
    '安全生产监督检查与事故预防措施',
    '组织结构调整与人力资源配置策略',
    '全球经济形势变化对企业战略调整的影响分析与实施指南-2023年8月份第1版(23)',
    '先进技术研发项目管理与市场推广计划-2023年8月份第2版(57)',
    '企业品牌重塑与市场定位提升策略-2023年8月份第14版(59)',
    '跨文化交流能力培养与国际业务扩展-2023年8月份第15版(68)',
    '财务审计流程与内部控制系统建设',
    '年度财务报告分析',
    '市场调研结果总结',
    '新产品开发计划',
    '员工培训手册',
    '供应链优化报告',
    'f34feee2cc5b4ac8a3ed3b656f93f2a2',
    'c9f0f895fb98ab9159f51fd0297e236d',
    '3e7e6f8b34134b5a83dabe8a73764e6f',
    'b2b7c9e5d4824ff298234501790f08e9',
    '质量控制手册',
    '企业社会责任报告',
    '竞争对手分析报告',
    '风险管理框架文件',
    '企业文化介绍',
    '云计算技术在企业运营中的应用优势-2023年8月份第16版(90)',
    '高性能工作团队建设与绩效提升方法-2023年8月份第17版(52)',
    '节能减排技术改造投资回报分析-2023年8月份第18版(39)',
    '知识产权战略在全球竞争中的作用-2023年8月份第19版(84)',
    '企业社会责任实施与品牌形象提升-2023年8月份第20版(71)',
    '7a3e630bdea44f19a7e10a3412bd6aec',
    '88d2582db0014652a854eab7b5e76a18',
    '4e5d84ea901d4306a0728c7c4d5f5128',
    '53a0acae36f24757a1e74fa4f45f1ee5',
    'cfcd208495d565ef66e7dff9f98764da',
    '7b774effe4a349c6dd82ad4f4f21d34c',
    'Financial Audit Process and Internal Control System Construction',
    'Annual Financial Report Analysis',
    'Market Research Results Summary',
    'New Product Development Plan',
    'Employee Training Manual',
    'Supply Chain Optimization Report',
    'Quality Control Manual',
    'Cross-Cultural Communication Skills Development and International Business Expansion - August 2023 Edition 15 (68)',
    'Advantages of Cloud Computing Technology in Enterprise Operations - August 2023 Edition 16 (90)',
    'High-Performance Team Building and Performance Improvement Methods - August 2023 Edition 17 (52)',
    'Investment Return Analysis of Energy-Saving and Emission-Reduction Technology Transformation - August 2023 Edition 18 (39)',
    'Role of Intellectual Property Strategy in Global Competition - August 2023 Edition 19 (84)',
    'Implementation of Corporate Social Responsibility and Brand Image Enhancement - August 2023 Edition 20 (71)',
  ]
  const suffixes = {
    image: ['jpg', 'png', 'gif'],
    document: ['pdf', 'doc', 'docx'],
    audio: ['mp3', 'wav'],
    video: ['mp4', 'avi'],
    package: ['zip', 'rar', 'gz'],
  }
  return Array.from({ length: 100 }, (_, index) => {
    // 使用id作为基础来生成“随机”但一致的数据
    const id = index + 1
    const fileType = fileTypes[(id - 1) % fileTypes.length]
    const mime_type = mimeTypes[fileType][(id - 1) % mimeTypes[fileType].length]
    const suffix = suffixes[fileType][(id - 1) % suffixes[fileType].length]
    return {
      id,
      storage_mode: storageModes[(id - 1) % storageModes.length],
      origin_name: `${fileNames[(id - 1) % fileNames.length]}.${suffix}`,
      object_name: `Object${12345678 * id}`,
      hash: `${id}`.padStart(10, '0'), // 示例：用id填充以生成固定长度的字符串
      mime_type,
      storage_path: `/path/to/resource_${id}`,
      suffix,
      size_byte: 1024 * id, // 示例：基于id的简单计算
      size_info: `${id}KB`, // 直接基于id生成
      url: `https://picsum.photos/300/300?random=${id}`,
      created_by: (id % 10) + 1,
      updated_by: (id % 10) + 1,
      created_at: `2023-01-01 12:03:40`,
      updated_at: `2023-01-01 12:03:40`,
      deleted_at: null,
      remark: `This is a remark for id ${id}.`,
    }
  })
}

export const attachments = generateData()
