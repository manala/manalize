# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

Vagrant.require_version ">= 1.7.2"

options = {
    :vendor           => '{{ vendor }}',
    :app              => '{{ app }}',
    :aliases          => [],
    :memory           => 1024,
    :box              => 'elao/symfony-standard-debian',
    :box_version      => '~> 1.0.0',
    :folders          => {
        '.' => '/srv/app/symfony'
    },
    :ansible_playbook => 'ansible/playbook.yml',
    :ansible_groups   => ['env_dev', 'app'],
    :ansible_vars     => {
        '_user' => 'vagrant'
    },
    :debug            => false
}

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    # Box
    config.vm.box         = options[:box]
    config.vm.box_version = options[:box_version]

    # Hostname
    config.vm.hostname = options[:app] + ((options[:vendor] != '') ? '.' + options[:vendor] : '') + '.dev'

    # Hosts
    if Vagrant.has_plugin?('landrush')
        config.landrush.enabled            = true
        config.landrush.tld                = config.vm.hostname
        config.landrush.guest_redirect_dns = false
    elsif Vagrant.has_plugin?('vagrant-hostmanager')
        config.hostmanager.enabled     = true
        config.hostmanager.manage_host = true
        config.hostmanager.ip_resolver = proc do |vm, resolving_vm|
            if vm.id
                `VBoxManage guestproperty get #{vm.id} "/VirtualBox/GuestInfo/Net/1/V4/IP"`.split()[1]
            end
        end
        if options[:aliases].any?
            config.hostmanager.aliases = ''
            for host in options[:aliases]
                config.hostmanager.aliases += host + '.' + config.vm.hostname + ' '
            end
        end
    end

    # Network
    config.vm.network 'private_network', type: 'dhcp'

    # Ssh
    config.ssh.forward_agent = true

    # Folders
    options[:folders].each do |host, guest|
        config.vm.synced_folder host, guest,
            type: 'nfs',
            mount_options: ['nolock', 'actimeo=1', 'fsc']
    end

    # Providers
    config.vm.provider :virtualbox do |vb|
        vb.name   = ((options[:vendor] != '') ? options[:vendor] + '_' : '') + options[:app]
        vb.memory = options[:memory]
        vb.gui    = options[:debug]
        vb.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
        vb.customize ['modifyvm', :id, '--natdnsproxy1', 'on']
    end

    # Git
    if File.exists?(File.join(Dir.home, '.gitconfig')) then
        config.vm.provision :file do |file|
            file.source      = '~/.gitconfig'
            file.destination = '/home/vagrant/.gitconfig'
        end
    end

    if File.exists?(File.join(Dir.home, '.gitignore')) then
        config.vm.provision :file do |file|
            file.source      = '~/.gitignore'
            file.destination = '/home/vagrant/.gitignore'
        end
    end

    # Composer
    if File.exists?(File.join(Dir.home, '.composer/auth.json')) then
        config.vm.provision :file do |file|
            file.source      = '~/.composer/auth.json'
            file.destination = '/home/vagrant/.composer/auth.json'
        end
    end

    # Provisioners
    config.vm.provision 'ansible' do |ansible|
        ansible.playbook      = options[:ansible_playbook]
        ansible.verbose       = options[:debug] ? 'vvvv' : false
        ansible.extra_vars    = options[:ansible_vars]
        ansible.sudo          = true
        ansible.raw_arguments = ['--force-handlers']
        ansible.groups        = {}
        for group in options[:ansible_groups]
            ansible.groups[group] = ['default']
        end
    end

end
